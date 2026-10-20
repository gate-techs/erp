@extends('layouts/contentNavbarLayout')

@section('title', __('Academic Monthly Plans') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Academic Management') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Academic Monthly Plans') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-calendar-alt text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Academic Monthly Plans') }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        @can('academic.monthly-plans.create')
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddAcademicMonthlyPlan" aria-controls="offcanvasAddAcademicMonthlyPlan">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add Academic Monthly Plan') }}</span>
        </button>
        @endcan
    </div>
</div>

<!-- Filters Card (3 per row) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Institution Filter -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Educational Institution / Nursery') }}</label>
                <select id="filter_institution_id" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                    <option value="">{{ __('All Institutions') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Branch Filter -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select id="filter_branch_id" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Period Filter (Academic Years) -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Years') }}</label>
                <select id="filter_academic_year_id" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                    <option value="">{{ __('All Periods') }}</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->translate(app()->getLocale())?->name ?? $year->translate('ar')?->name ?? $year->name ?? $year->code }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Academic Stage Filter -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Stage') }}</label>
                <select id="filter_academic_stage_id" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                    <option value="">{{ __('All Academic Stages') }}</option>
                    @foreach ($academicStages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->name ?? $stage->code }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 5. Academic Class Filter -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Class') }}</label>
                <select id="filter_academic_class_id" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                    <option value="">{{ __('All Academic Classes') }}</option>
                    @foreach ($academicClasses as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->translate(app()->getLocale())?->name ?? $cls->translate('ar')?->name ?? $cls->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 6. Status Filter -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}">{{ __($status === 'active' ? 'Active' : 'Inactive') }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 7. Reset Button in its own column -->
            <div class="col-12 col-md-4">
                <button type="button" id="btn_reset_filters" class="btn btn-label-secondary w-100 d-flex align-items-center justify-content-center gap-1">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span>{{ __('Reset') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alert Box for Ajax feedback -->
<div id="ajaxAlertContainer"></div>

<!-- DataTables Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="academicMonthlyPlansTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Monthly Plan Title') }}</th>
                    <th>{{ __('Institution') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th>{{ __('Academic Years') }}</th>
                    <th>{{ __('Academic Stage') }}</th>
                    <th>{{ __('Academic Classes') }}</th>
                    <th>{{ __('Dates') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@can('academic.monthly-plans.create')
<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add Academic Monthly Plan                                    -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddAcademicMonthlyPlan" aria-labelledby="offcanvasAddAcademicMonthlyPlanLabel" style="width: 580px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddAcademicMonthlyPlanLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-plus-circle text-primary font-22"></i>
            <span>{{ __('Add Academic Monthly Plan') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="addAcademicMonthlyPlanForm" action="{{ route('academic.monthly-plans.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- 1. Image Upload Box (Default to Logo if empty) -->
            <div class="mb-4">
                <label class="form-label fw-semibold mb-2 text-heading d-flex align-items-center justify-content-between">
                    <span>{{ __('Add New Image') }}</span>
                    <small class="text-muted">PNG, JPG, WEBP</small>
                </label>
                <div class="border rounded-3 p-3 text-center position-relative bg-lighter d-flex flex-column align-items-center justify-content-center" 
                     style="border-style: dashed !important; border-width: 2px !important; cursor: pointer; min-height: 110px;"
                     onclick="document.getElementById('add_plan_image').click();">
                    
                    <div id="add_image_placeholder" class="d-flex align-items-center justify-content-between w-100 px-2">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/img/favicon/favicon.ico') }}" alt="Logo" class="rounded object-fit-contain border bg-white p-1" width="36" height="36">
                            <span class="text-muted small">{{ __('Add New Image') }}</span>
                        </div>
                        <i class="icon-base bx bx-camera text-primary font-24"></i>
                    </div>

                    <div id="add_image_preview_box" class="d-none align-items-center gap-2 w-100">
                        <img id="add_image_preview" src="" alt="Preview" class="rounded object-fit-cover border" width="56" height="56">
                        <div class="text-start">
                            <span id="add_image_name" class="fw-semibold small d-block text-truncate" style="max-width: 220px;"></span>
                            <small class="text-primary">{{ __('Click to change image') }}</small>
                        </div>
                    </div>

                    <input type="file" id="add_plan_image" name="image" class="d-none" accept="image/*" onchange="previewAddImage(this)">
                </div>
                <div class="invalid-feedback d-block" id="add_image_error"></div>
            </div>

            <!-- 2. Institution (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Educational Institution / Nursery') }} <span class="text-danger">*</span></label>
                <select name="institution_id" id="add_institution_id" class="select2 select2-selection__rendered form-select" required data-allow-clear="true">
                    <option value="">{{ __('Select Institution...') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_institution_id_error"></div>
            </div>

            <!-- 3. Branch (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Branch') }}</label>
                <select name="branch_id" id="add_branch_id" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                    <option value="">{{ __('Select Branch...') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_branch_id_error"></div>
            </div>

            <!-- 4. Period / Academic Year (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Academic Years') }}</label>
                <select name="academic_year_id" id="add_academic_year_id" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                    <option value="">{{ __('Select Period...') }}</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->translate(app()->getLocale())?->name ?? $year->translate('ar')?->name ?? $year->name ?? $year->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_academic_year_id_error"></div>
            </div>

            <!-- 5. Academic Stage (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Stage') }} <span class="text-danger">*</span></label>
                <select name="academic_stage_id" id="add_academic_stage_id" class="select2 select2-selection__rendered form-select" required data-allow-clear="true">
                    <option value="">{{ __('Select Stage...') }}</option>
                    @foreach ($academicStages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->name ?? $stage->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_academic_stage_id_error"></div>
            </div>

            <!-- 6. Academic Classes (Multi-Select, Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Classes (Multi-Select)') }} <span class="text-danger">*</span></label>
                <select name="academic_class_ids[]" id="add_academic_class_ids" class="select2 select2-selection__rendered form-select" multiple="multiple" required>
                    @foreach ($academicClasses as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->translate(app()->getLocale())?->name ?? $cls->translate('ar')?->name ?? $cls->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_academic_class_ids_error"></div>
            </div>

            <!-- 7. Monthly Plan Title Arabic (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Monthly Plan Title (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="title_ar" id="add_title_ar" class="form-control" required maxlength="255">
                <div class="invalid-feedback d-block" id="add_title_ar_error"></div>
            </div>

            <!-- 8. Monthly Plan Title English (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Monthly Plan Title (English)') }} <span class="text-danger">*</span></label>
                <input type="text" name="title_en" id="add_title_en" class="form-control" required maxlength="255" dir="ltr">
                <div class="invalid-feedback d-block" id="add_title_en_error"></div>
            </div>

            <!-- 9. Start Date (Full Row with Flatpickr) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                <input type="text" name="start_date" id="add_start_date" class="form-control js-flatpickr-date flatpickr-input" required autocomplete="off">
                <div class="invalid-feedback d-block" id="add_start_date_error"></div>
            </div>

            <!-- 10. End Date (Full Row with Flatpickr) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('End Date') }} <span class="text-danger">*</span></label>
                <input type="text" name="end_date" id="add_end_date" class="form-control js-flatpickr-date flatpickr-input" required autocomplete="off">
                <div class="invalid-feedback d-block" id="add_end_date_error"></div>
            </div>

            <!-- 11. Status (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Status') }}</label>
                <select name="status" id="add_status" class="select2 select2-selection__rendered form-select">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" {{ $status === 'active' ? 'selected' : '' }}>{{ __($status === 'active' ? 'Active' : 'Inactive') }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_status_error"></div>
            </div>

            <!-- 12. Description Arabic (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Monthly Plan Description (Arabic)') }}</label>
                <textarea name="description_ar" id="add_description_ar" class="form-control" rows="3"></textarea>
                <div class="invalid-feedback d-block" id="add_description_ar_error"></div>
            </div>

            <!-- 13. Description English (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Monthly Plan Description (English)') }}</label>
                <textarea name="description_en" id="add_description_en" class="form-control" rows="3" dir="ltr"></textarea>
                <div class="invalid-feedback d-block" id="add_description_en_error"></div>
            </div>

            <!-- 14. Multi-file Attachments (Full Row) -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between">
                    <span><i class="icon-base bx bx-paperclip me-1"></i>{{ __('Documents & Attachments') }}</span>
                    <small class="text-muted">{{ __('Multiple files allowed (PDF, DOCX, Images)') }}</small>
                </label>
                <input type="file" name="attachments[]" id="add_attachments" class="form-control" multiple>
                <div class="invalid-feedback d-block" id="add_attachments_error"></div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" id="btn_submit_add" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-check font-18"></i>
                    <span>{{ __('Save Academic Monthly Plan') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@can('academic.monthly-plans.edit')
<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Academic Monthly Plan                                   -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditAcademicMonthlyPlan" aria-labelledby="offcanvasEditAcademicMonthlyPlanLabel" style="width: 580px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditAcademicMonthlyPlanLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-primary font-22"></i>
            <span>{{ __('Edit Academic Monthly Plan') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="editAcademicMonthlyPlanForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_plan_id" name="id">

            <!-- 1. Image Upload Box (Default to Logo if empty) -->
            <div class="mb-4">
                <label class="form-label fw-semibold mb-2 text-heading d-flex align-items-center justify-content-between">
                    <span>{{ __('Add New Image') }}</span>
                    <small class="text-muted">PNG, JPG, WEBP</small>
                </label>
                <div class="border rounded-3 p-3 text-center position-relative bg-lighter d-flex flex-column align-items-center justify-content-center" 
                     style="border-style: dashed !important; border-width: 2px !important; cursor: pointer; min-height: 110px;"
                     onclick="document.getElementById('edit_plan_image').click();">
                    
                    <div id="edit_image_placeholder" class="d-flex align-items-center justify-content-between w-100 px-2">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/img/favicon/favicon.ico') }}" alt="Logo" class="rounded object-fit-contain border bg-white p-1" width="36" height="36">
                            <span class="text-muted small">{{ __('Add New Image') }}</span>
                        </div>
                        <i class="icon-base bx bx-camera text-primary font-24"></i>
                    </div>

                    <div id="edit_image_preview_box" class="d-none align-items-center gap-2 w-100">
                        <img id="edit_image_preview" src="" alt="Preview" class="rounded object-fit-cover border" width="56" height="56">
                        <div class="text-start">
                            <span id="edit_image_name" class="fw-semibold small d-block text-truncate" style="max-width: 220px;"></span>
                            <small class="text-primary">{{ __('Click to change image') }}</small>
                        </div>
                    </div>

                    <input type="file" id="edit_plan_image" name="image" class="d-none" accept="image/*" onchange="previewEditImage(this)">
                </div>
                <div class="invalid-feedback d-block" id="edit_image_error"></div>
            </div>

            <!-- 2. Institution (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Educational Institution / Nursery') }} <span class="text-danger">*</span></label>
                <select name="institution_id" id="edit_institution_id" class="select2 select2-selection__rendered form-select" required data-allow-clear="true">
                    <option value="">{{ __('Select Institution...') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_institution_id_error"></div>
            </div>

            <!-- 3. Branch (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Branch') }}</label>
                <select name="branch_id" id="edit_branch_id" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                    <option value="">{{ __('Select Branch...') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_branch_id_error"></div>
            </div>

            <!-- 4. Period / Academic Year (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Academic Years') }}</label>
                <select name="academic_year_id" id="edit_academic_year_id" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                    <option value="">{{ __('Select Period...') }}</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->translate(app()->getLocale())?->name ?? $year->translate('ar')?->name ?? $year->name ?? $year->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_academic_year_id_error"></div>
            </div>

            <!-- 5. Academic Stage (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Stage') }} <span class="text-danger">*</span></label>
                <select name="academic_stage_id" id="edit_academic_stage_id" class="select2 select2-selection__rendered form-select" required data-allow-clear="true">
                    <option value="">{{ __('Select Stage...') }}</option>
                    @foreach ($academicStages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->name ?? $stage->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_academic_stage_id_error"></div>
            </div>

            <!-- 6. Academic Classes (Multi-Select, Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Classes (Multi-Select)') }} <span class="text-danger">*</span></label>
                <select name="academic_class_ids[]" id="edit_academic_class_ids" class="select2 select2-selection__rendered form-select" multiple="multiple" required>
                    @foreach ($academicClasses as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->translate(app()->getLocale())?->name ?? $cls->translate('ar')?->name ?? $cls->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_academic_class_ids_error"></div>
            </div>

            <!-- 7. Monthly Plan Title Arabic (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Monthly Plan Title (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="title_ar" id="edit_title_ar" class="form-control" required maxlength="255">
                <div class="invalid-feedback d-block" id="edit_title_ar_error"></div>
            </div>

            <!-- 8. Monthly Plan Title English (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Monthly Plan Title (English)') }} <span class="text-danger">*</span></label>
                <input type="text" name="title_en" id="edit_title_en" class="form-control" required maxlength="255" dir="ltr">
                <div class="invalid-feedback d-block" id="edit_title_en_error"></div>
            </div>

            <!-- 9. Start Date (Full Row with Flatpickr) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                <input type="text" name="start_date" id="edit_start_date" class="form-control js-flatpickr-date flatpickr-input" required autocomplete="off">
                <div class="invalid-feedback d-block" id="edit_start_date_error"></div>
            </div>

            <!-- 10. End Date (Full Row with Flatpickr) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('End Date') }} <span class="text-danger">*</span></label>
                <input type="text" name="end_date" id="edit_end_date" class="form-control js-flatpickr-date flatpickr-input" required autocomplete="off">
                <div class="invalid-feedback d-block" id="edit_end_date_error"></div>
            </div>

            <!-- 11. Status (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Status') }}</label>
                <select name="status" id="edit_status" class="select2 select2-selection__rendered form-select">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}">{{ __($status === 'active' ? 'Active' : 'Inactive') }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_status_error"></div>
            </div>

            <!-- 12. Description Arabic (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Monthly Plan Description (Arabic)') }}</label>
                <textarea name="description_ar" id="edit_description_ar" class="form-control" rows="3"></textarea>
                <div class="invalid-feedback d-block" id="edit_description_ar_error"></div>
            </div>

            <!-- 13. Description English (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Monthly Plan Description (English)') }}</label>
                <textarea name="description_en" id="edit_description_en" class="form-control" rows="3" dir="ltr"></textarea>
                <div class="invalid-feedback d-block" id="edit_description_en_error"></div>
            </div>

            <!-- 14. Existing Attachments List -->
            <div class="mb-3" id="edit_existing_attachments_container" style="display: none;">
                <label class="form-label fw-semibold text-heading mb-2">{{ __('Current Attachments') }}</label>
                <div class="list-group list-group-flush border rounded-3" id="edit_existing_attachments_list"></div>
            </div>

            <!-- 15. Add More Attachments (Full Row) -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between">
                    <span><i class="icon-base bx bx-paperclip me-1"></i>{{ __('Add More Attachments') }}</span>
                    <small class="text-muted">{{ __('Multiple files allowed') }}</small>
                </label>
                <input type="file" name="attachments[]" id="edit_attachments" class="form-control" multiple>
                <div class="invalid-feedback d-block" id="edit_attachments_error"></div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" id="btn_submit_edit" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-check font-18"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@can('academic.monthly-plans.view')
<!-- ========================================================================= -->
<!-- 3. Modal: View Plan Details                                               -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalViewMonthlyPlan" tabindex="-1" aria-labelledby="modalViewMonthlyPlanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-heading d-flex align-items-center gap-2 mb-0" id="modalViewMonthlyPlanLabel">
                    <i class="icon-base bx bx-calendar-alt text-primary font-22"></i>
                    <span id="view_plan_title">{{ __('Plan Details') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Left info box -->
                    <div class="col-12 col-md-4 text-start">
                        <div class="text-center mb-3">
                            <img id="view_plan_image" src="" alt="Plan Image" class="rounded object-fit-cover border shadow-sm" width="90" height="90" onerror="this.src='{{ asset('assets/img/favicon/favicon.ico') }}'">
                        </div>
                        <div class="p-3 bg-light rounded">
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Institution') }}:</small> <span id="view_inst_name" class="fw-semibold text-heading">-</span></div>
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Branch') }}:</small> <span id="view_branch_name" class="fw-semibold text-heading">-</span></div>
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Academic Years') }}:</small> <span id="view_period_name" class="badge bg-label-info">-</span></div>
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Academic Stage') }}:</small> <span id="view_stage_name" class="badge bg-label-primary">-</span></div>
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Start / End Dates') }}:</small> <span id="view_dates" class="fw-semibold text-heading small">-</span></div>
                        </div>
                    </div>

                    <!-- Right content box -->
                    <div class="col-12 col-md-8">
                        <h5 class="fw-bold text-primary mb-1" id="view_title_ar">-</h5>
                        <h6 class="text-muted mb-3" id="view_title_en">-</h6>

                        <!-- Classes Badges in View -->
                        <div class="mb-3">
                            <h6 class="fw-bold text-heading mb-1">{{ __('Academic Classes') }}:</h6>
                            <div id="view_classes_badges" class="d-flex flex-wrap gap-1"></div>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-bold text-heading mb-1">{{ __('Monthly Plan Description (Arabic)') }}:</h6>
                            <div id="view_description_ar" class="p-3 bg-light rounded text-break" style="white-space: pre-line; min-height: 50px;">-</div>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-bold text-heading mb-1">{{ __('Monthly Plan Description (English)') }}:</h6>
                            <div id="view_description_en" class="p-3 bg-light rounded text-break" style="white-space: pre-line; min-height: 50px;" dir="ltr">-</div>
                        </div>

                        <!-- Attachments in View Modal -->
                        <div>
                            <h6 class="fw-bold text-heading mb-2">{{ __('Documents & Attachments') }}:</h6>
                            <div class="list-group list-group-flush border rounded-3" id="view_attachments_list"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-4">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
@endcan

@can('academic.monthly-plans.delete')
<!-- ========================================================================= -->
<!-- 4. Modal: Delete Confirmation                                             -->
<!-- ========================================================================= -->
<div class="modal fade" id="deleteAcademicMonthlyPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-trash font-22"></i>
                    <span>{{ __('Confirm Delete') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="icon-base bx bx-trash font-30"></i>
                </div>
                <h5 class="mb-2 fw-semibold">{{ __('Are you sure you want to delete the monthly plan') }}?</h5>
                <p class="text-muted small mb-0" id="delete_target_name"></p>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="confirm_delete_btn" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection

@section('page-script')
<script>
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
            document.getElementById('edit_image_placeholder').classList.add('d-none');
            document.getElementById('edit_image_preview_box').classList.remove('d-none');
            document.getElementById('edit_image_preview_box').classList.add('d-flex');
        };
        reader.readAsDataURL(file);
    }
}

(function () {
    function initAcademicMonthlyPlans() {
        const $ = window.jQuery || window.$;
        if (!$ || !$.fn.DataTable) {
            setTimeout(initAcademicMonthlyPlans, 50);
            return;
        }

        const baseUrl = "{{ url('academic/monthly-plans') }}";
        const dataUrl = "{{ route('academic.monthly-plans.data') }}";

        // Setup CSRF header for jQuery AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        // Helper: Show Alert
        function showAlert(type, message) {
            const icon = type === 'success' ? 'bx-check-circle' : 'bx-error-circle';
            const html = `
                <div class="alert alert-${type} alert-dismissible d-flex align-items-center mb-4 fade show" role="alert">
                    <i class="icon-base bx ${icon} me-2 font-20"></i>
                    <div>${message}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            $('#ajaxAlertContainer').html(html);
            window.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(() => {
                $('#ajaxAlertContainer .alert').alert('close');
            }, 5000);
        }

        // Helper: Clear validation errors
        function clearFormErrors(formId) {
            $(`#${formId} .is-invalid`).removeClass('is-invalid');
            $(`#${formId} .invalid-feedback`).text('');
        }

        // Helper: Show validation errors
        function showFormErrors(formId, errors) {
            clearFormErrors(formId);
            for (const field in errors) {
                const cleanField = field.replace(/\.\d+/g, '');
                const input = $(`#${formId} [name="${field}"], #${formId} [name="${field}[]"], #${formId} [name="${cleanField}[]"]`);
                input.addClass('is-invalid');
                const errorDiv = $(`#${formId}_${cleanField}_error`) || $(`#${formId} #${formId}_${cleanField}_error`);
                if (errorDiv.length) {
                    errorDiv.text(errors[field][0]);
                } else {
                    input.closest('.mb-3, .mb-4').find('.invalid-feedback').text(errors[field][0]);
                }
            }
        }

        // Helper to initialize Select2
        function applySelect2(context) {
            const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';
            const target = context ? $(context).find('select.select2, select.select2-selection__rendered') : $('select.select2, select.select2-selection__rendered');
            target.each(function () {
                const $this = $(this);
                if ($this.hasClass('select2-hidden-accessible')) {
                    $this.select2('destroy');
                }
                const $parent = $this.closest('.offcanvas, .modal');
                $this.select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $parent.length ? $parent : null,
                    placeholder: '',
                    allowClear: $this.data('allow-clear') !== false,
                    dir: isRtl ? 'rtl' : 'ltr',
                    width: '100%'
                });
            });
        }

        // Helper to initialize Flatpickr
        function applyFlatpickr(context) {
            const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';
            const target = context ? $(context).find('.js-flatpickr-date') : $('.js-flatpickr-date');
            target.each(function () {
                if (window.flatpickr) {
                    if (this._flatpickr) {
                        this._flatpickr.destroy();
                    }
                    window.flatpickr(this, {
                        dateFormat: 'Y-m-d',
                        allowInput: true,
                        disableMobile: true,
                        position: isRtl ? 'auto right' : 'auto left'
                    });
                }
            });
        }

        // Initialize plugins on document ready
        applySelect2();
        applyFlatpickr();

        // Offcanvas fix on shown
        $('#offcanvasAddAcademicMonthlyPlan, #offcanvasEditAcademicMonthlyPlan').on('shown.bs.offcanvas', function () {
            applySelect2(this);
            applyFlatpickr(this);
        });

        // Initialize Server-Side DataTable matching the theme style
        const table = $('#academicMonthlyPlansTable').DataTable({
            processing: true,
            serverSide: true,
            pagingType: 'simple_numbers',
            language: {
                emptyTable: `
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="icon-base bx bx-calendar-event font-32 text-secondary"></i>
                        </div>
                        <h6 class="fw-bold mb-1">{{ __('No academic monthly plans found') }}</h6>
                        <p class="text-muted small mb-0">{{ __('Click "Add Academic Monthly Plan" to create the first plan.') }}</p>
                    </div>
                `
            },
            ajax: {
                url: dataUrl,
                data: function (d) {
                    d.institution_id = $('#filter_institution_id').val();
                    d.branch_id = $('#filter_branch_id').val();
                    d.academic_year_id = $('#filter_academic_year_id').val();
                    d.academic_stage_id = $('#filter_academic_stage_id').val();
                    d.academic_class_id = $('#filter_academic_class_id').val();
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
                { data: 'plan_info', name: 'translations.title', className: 'fw-semibold text-heading' },
                { data: 'institution_name', name: 'institution_id', defaultContent: '-' },
                { data: 'branch_name', name: 'branch_id', defaultContent: '-' },
                { data: 'period_name', name: 'academic_year_id', defaultContent: '-' },
                { data: 'stage_name', name: 'academic_stage_id', defaultContent: '-' },
                { data: 'classes_badges', name: 'classes.id', orderable: false, defaultContent: '-' },
                { data: 'dates_formatted', name: 'start_date', defaultContent: '-' },
                { data: 'status_badge', name: 'status', className: 'text-center' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[0, 'desc']]
        });

        // Filter Change Listeners
        $('#filter_institution_id, #filter_branch_id, #filter_academic_year_id, #filter_academic_stage_id, #filter_academic_class_id, #filter_status').on('change', function () {
            table.ajax.reload();
        });

        // Reset Filters Listener
        $('#btn_reset_filters').on('click', function () {
            $('#filter_institution_id').val('').trigger('change');
            $('#filter_branch_id').val('').trigger('change');
            $('#filter_academic_year_id').val('').trigger('change');
            $('#filter_academic_stage_id').val('').trigger('change');
            $('#filter_academic_class_id').val('').trigger('change');
            $('#filter_status').val('').trigger('change');
            table.ajax.reload();
        });

        // Dynamic Cascading Dropdowns
        function loadCascades(instId, prefix, selectedBranch = '', selectedYear = '', selectedStage = '', selectedClasses = []) {
            const branchSel = `#${prefix}_branch_id`;
            const yearSel = `#${prefix}_academic_year_id`;
            const stageSel = `#${prefix}_academic_stage_id`;
            const classSel = `#${prefix}_academic_class_ids`;

            if (!instId) {
                $(branchSel).html('<option value="">{{ __("Select Branch...") }}</option>').trigger('change');
                $(yearSel).html('<option value="">{{ __("Select Period...") }}</option>').trigger('change');
                $(stageSel).html('<option value="">{{ __("Select Stage...") }}</option>').trigger('change');
                $(classSel).html('').trigger('change');
                return;
            }

            // 1. Branches
            $.get("{{ url('academic/monthly-plans-ajax/branches') }}/" + instId, function (res) {
                if (res.success) {
                    var options = '<option value="">{{ __("Select Branch...") }}</option>';
                    $.each(res.data, function (i, item) {
                        var isSelected = (String(item.id) === String(selectedBranch)) ? 'selected' : '';
                        options += '<option value="' + item.id + '" ' + isSelected + '>' + item.name + '</option>';
                    });
                    $(branchSel).html(options).trigger('change');
                }
            });

            // 2. Periods / Academic Years
            $.get("{{ url('academic/monthly-plans-ajax/periods') }}/" + instId, function (res) {
                if (res.success) {
                    var options = '<option value="">{{ __("Select Period...") }}</option>';
                    $.each(res.data, function (i, item) {
                        var isSelected = (String(item.id) === String(selectedYear)) ? 'selected' : '';
                        options += '<option value="' + item.id + '" ' + isSelected + '>' + item.name + '</option>';
                    });
                    $(yearSel).html(options).trigger('change');
                }
            });

            // 3. Stages
            $.get("{{ url('academic/monthly-plans-ajax/stages') }}/" + instId, function (res) {
                if (res.success) {
                    var options = '<option value="">{{ __("Select Stage...") }}</option>';
                    $.each(res.data, function (i, item) {
                        var isSelected = (String(item.id) === String(selectedStage)) ? 'selected' : '';
                        options += '<option value="' + item.id + '" ' + isSelected + '>' + item.name + '</option>';
                    });
                    $(stageSel).html(options).trigger('change');
                }
            });

            // 4. Classes
            loadClasses(prefix, selectedClasses);
        }

        function loadClasses(prefix, selectedClasses = []) {
            const instId = $(`#${prefix}_institution_id`).val();
            const branchId = $(`#${prefix}_branch_id`).val();
            const stageId = $(`#${prefix}_academic_stage_id`).val();
            const yearId = $(`#${prefix}_academic_year_id`).val();
            const classSel = `#${prefix}_academic_class_ids`;

            $.get("{{ route('academic.monthly-plans.ajax.classes') }}", {
                institution_id: instId,
                branch_id: branchId,
                academic_stage_id: stageId,
                academic_year_id: yearId
            }, function (res) {
                if (res.success) {
                    var options = '';
                    const selectedArray = Array.isArray(selectedClasses) ? selectedClasses.map(String) : [];
                    $.each(res.data, function (i, item) {
                        var isSelected = selectedArray.includes(String(item.id)) ? 'selected' : '';
                        options += '<option value="' + item.id + '" ' + isSelected + '>' + item.name + '</option>';
                    });
                    $(classSel).html(options).trigger('change');
                }
            });
        }

        $('#add_institution_id').on('change', function () {
            loadCascades($(this).val(), 'add');
        });
        $('#add_branch_id, #add_academic_stage_id, #add_academic_year_id').on('change', function () {
            loadClasses('add');
        });

        $('#edit_institution_id').on('change', function () {
            var currentBranch = $('#edit_branch_id').val();
            var currentYear = $('#edit_academic_year_id').val();
            var currentStage = $('#edit_academic_stage_id').val();
            var currentClasses = $('#edit_academic_class_ids').val();
            loadCascades($(this).val(), 'edit', currentBranch, currentYear, currentStage, currentClasses);
        });
        $('#edit_branch_id, #edit_academic_stage_id, #edit_academic_year_id').on('change', function () {
            var currentClasses = $('#edit_academic_class_ids').val();
            loadClasses('edit', currentClasses);
        });

        // Sync Arabic & English titles automatically
        $('#add_title_ar').on('input', function () {
            if (!$('#add_title_en').data('manually_typed')) {
                $('#add_title_en').val($(this).val());
            }
        });
        $('#add_title_en').on('input', function () {
            $(this).data('manually_typed', true);
        });

        $('#edit_title_ar').on('input', function () {
            if (!$('#edit_title_en').data('manually_typed')) {
                $('#edit_title_en').val($(this).val());
            }
        });
        $('#edit_title_en').on('input', function () {
            $(this).data('manually_typed', true);
        });

        // 1. Submit Add Academic Monthly Plan
        $('#addAcademicMonthlyPlanForm').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = $('#btn_submit_add');
            const originalBtnHtml = submitBtn.html();

            clearFormErrors('addAcademicMonthlyPlanForm');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Saving...") }}');

            const formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    const offcanvasEl = document.getElementById('offcanvasAddAcademicMonthlyPlan');
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                    if (bsOffcanvas) bsOffcanvas.hide();

                    form[0].reset();
                    $('#add_title_en').data('manually_typed', false);
                    form.find('select.select2, select.select2-selection__rendered').val('').trigger('change');
                    $('#add_status').val('active').trigger('change');
                    $('#add_plan_image').val('');
                    $('#add_image_preview').attr('src', '');
                    $('#add_image_name').text('');
                    $('#add_image_preview_box').removeClass('d-flex').addClass('d-none');
                    $('#add_image_placeholder').removeClass('d-none');

                    showAlert('success', res.message || '{{ __("Academic monthly plan created successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        showFormErrors('addAcademicMonthlyPlanForm', xhr.responseJSON.errors);
                    } else {
                        const msg = xhr.responseJSON?.message || '{{ __("Error saving academic monthly plan.") }}';
                        showAlert('danger', msg);
                    }
                }
            });
        });

        // 2. Open Edit Offcanvas & Fetch Data
        const editOffcanvasEl = document.getElementById('offcanvasEditAcademicMonthlyPlan');
        const editOffcanvas = new bootstrap.Offcanvas(editOffcanvasEl);

        $(document).on('click', '.btn-edit-monthly-plan', function () {
            const id = $(this).data('id');
            clearFormErrors('editAcademicMonthlyPlanForm');
            $('#edit_title_en').data('manually_typed', false);

            $.ajax({
                url: `${baseUrl}/${id}`,
                type: 'GET',
                success: function (res) {
                    if (res.success && res.data) {
                        const item = res.data;

                        $('#edit_plan_id').val(item.id);
                        $('#edit_institution_id').val(item.institution_id ? String(item.institution_id) : '').trigger('change');
                        $('#edit_branch_id').val(item.branch_id ? String(item.branch_id) : '').trigger('change');
                        $('#edit_academic_year_id').val(item.academic_year_id ? String(item.academic_year_id) : '').trigger('change');
                        $('#edit_academic_stage_id').val(item.academic_stage_id ? String(item.academic_stage_id) : '').trigger('change');
                        $('#edit_academic_class_ids').val(item.academic_class_ids || []).trigger('change');

                        $('#edit_title_ar').val(item.title_ar || '');
                        $('#edit_title_en').val(item.title_en || '');
                        $('#edit_description_ar').val(item.description_ar || '');
                        $('#edit_description_en').val(item.description_en || '');
                        $('#edit_start_date').val(item.start_date || '');
                        $('#edit_end_date').val(item.end_date || '');
                        $('#edit_status').val(item.status || 'active').trigger('change');
                        $('#edit_plan_image').val('');
                        $('#edit_attachments').val('');

                        // Image Preview
                        const editPreviewImg = document.getElementById('edit_image_preview');
                        const editPreviewName = document.getElementById('edit_image_name');
                        const editPreviewBox = document.getElementById('edit_image_preview_box');
                        const editPlaceholder = document.getElementById('edit_image_placeholder');

                        if (item.image_url) {
                            editPreviewImg.src = item.image_url;
                            editPreviewName.textContent = item.has_custom_image ? '{{ __("Current Image") }}' : '{{ __("Default Logo") }}';
                            editPlaceholder.classList.add('d-none');
                            editPreviewBox.classList.remove('d-none');
                            editPreviewBox.classList.add('d-flex');
                        } else {
                            editPreviewImg.src = '';
                            editPreviewName.textContent = '';
                            editPreviewBox.classList.remove('d-flex');
                            editPreviewBox.classList.add('d-none');
                            editPlaceholder.classList.remove('d-none');
                        }

                        // Existing Attachments List
                        const $attachContainer = $('#edit_existing_attachments_container');
                        const $attachList = $('#edit_existing_attachments_list');
                        $attachList.empty();

                        if (item.attachments && item.attachments.length > 0) {
                            item.attachments.forEach(att => {
                                const row = `
                                    <div class="list-group-item d-flex align-items-center justify-content-between py-2 px-3" id="attachment_row_${att.id}">
                                        <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 70%;">
                                            <i class="icon-base bx bx-file text-primary font-20 flex-shrink-0"></i>
                                            <div class="text-truncate">
                                                <span class="fw-semibold small d-block text-truncate">${att.file_name}</span>
                                                <small class="text-muted">${att.formatted_size || ''}</small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <a href="${baseUrl}/attachments/${att.id}/download" class="btn btn-xs btn-icon btn-label-primary" title="{{ __('Download') }}">
                                                <i class="icon-base bx bx-download"></i>
                                            </a>
                                            <button type="button" class="btn btn-xs btn-icon btn-label-danger btn-delete-attachment" data-id="${att.id}" title="{{ __('Delete') }}">
                                                <i class="icon-base bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                `;
                                $attachList.append(row);
                            });
                            $attachContainer.show();
                        } else {
                            $attachContainer.hide();
                        }

                        $('#editAcademicMonthlyPlanForm').attr('action', `${baseUrl}/${item.id}`);
                        editOffcanvas.show();
                    }
                },
                error: function () {
                    showAlert('danger', '{{ __("Failed to load academic monthly plan data.") }}');
                }
            });
        });

        // 3. Submit Edit Academic Monthly Plan
        $('#editAcademicMonthlyPlanForm').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = $('#btn_submit_edit');
            const originalBtnHtml = submitBtn.html();
            const actionUrl = form.attr('action');

            clearFormErrors('editAcademicMonthlyPlanForm');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Updating...") }}');

            const formData = new FormData(this);

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    editOffcanvas.hide();
                    showAlert('success', res.message || '{{ __("Academic monthly plan updated successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        showFormErrors('editAcademicMonthlyPlanForm', xhr.responseJSON.errors);
                    } else {
                        const msg = xhr.responseJSON?.message || '{{ __("Error updating academic monthly plan.") }}';
                        showAlert('danger', msg);
                    }
                }
            });
        });

        // 4. View Monthly Plan Modal
        const viewModalEl = document.getElementById('modalViewMonthlyPlan');
        const viewModal = new bootstrap.Modal(viewModalEl);

        $(document).on('click', '.btn-view-monthly-plan', function () {
            const id = $(this).data('id');
            $.ajax({
                url: `${baseUrl}/${id}`,
                type: 'GET',
                success: function (res) {
                    if (res.success && res.data) {
                        const d = res.data;
                        $('#view_plan_title').text(d.title || d.title_ar || "{{ __('Plan Details') }}");
                        $('#view_title_ar').text(d.title_ar || '-');
                        $('#view_title_en').text(d.title_en || '-');
                        $('#view_description_ar').text(d.description_ar || "{{ __('No description provided.') }}");
                        $('#view_description_en').text(d.description_en || "{{ __('No description provided.') }}");
                        $('#view_inst_name').text(d.institution_name || '-');
                        $('#view_branch_name').text(d.branch_name || '-');
                        $('#view_period_name').text(d.period_name || '-');
                        $('#view_stage_name').text(d.stage_name || '-');
                        $('#view_dates').text((d.start_date || '...') + ' → ' + (d.end_date || '...'));
                        $('#view_plan_image').attr('src', d.image_url || "{{ asset('assets/img/favicon/favicon.ico') }}");

                        // Classes Badges in View Modal
                        var classesHtml = '';
                        if (d.classes_list && d.classes_list.length > 0) {
                            $.each(d.classes_list, function (i, cls) {
                                classesHtml += `<span class="badge bg-label-info">${cls.name}</span>`;
                            });
                        } else {
                            classesHtml = '<span class="text-muted small">-</span>';
                        }
                        $('#view_classes_badges').html(classesHtml);

                        // Attachments List in Modal
                        var attHtml = '';
                        if (d.attachments && d.attachments.length > 0) {
                            $.each(d.attachments, function (i, att) {
                                attHtml += `
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                        <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 75%;">
                                            <i class="icon-base bx bx-file text-primary font-20 flex-shrink-0"></i>
                                            <div class="text-truncate">
                                                <span class="fw-semibold small d-block text-truncate">${att.file_name}</span>
                                                <small class="text-muted">${att.formatted_size || ''}</small>
                                            </div>
                                        </div>
                                        <a href="${baseUrl}/attachments/${att.id}/download" class="btn btn-xs btn-icon btn-label-primary" title="{{ __('Download') }}">
                                            <i class="icon-base bx bx-download"></i>
                                        </a>
                                    </div>
                                `;
                            });
                        } else {
                            attHtml = '<span class="text-muted small text-center p-3 d-block">{{ __("No attachments uploaded.") }}</span>';
                        }
                        $('#view_attachments_list').html(attHtml);

                        viewModal.show();
                    }
                }
            });
        });

        // 5. Delete Attachment in Edit Offcanvas
        $(document).on('click', '.btn-delete-attachment', function () {
            const attId = $(this).data('id');
            const row = $(`#attachment_row_${attId}`);

            if (!confirm('{{ __("Delete Attachment?") }}')) return;

            $.ajax({
                url: `${baseUrl}/attachments/${attId}`,
                type: 'DELETE',
                success: function (res) {
                    row.fadeOut(300, function () {
                        $(this).remove();
                        if ($('#edit_existing_attachments_list').children().length === 0) {
                            $('#edit_existing_attachments_container').hide();
                        }
                    });
                    table.ajax.reload(null, false);
                },
                error: function () {
                    alert('{{ __("Failed to delete attachment.") }}');
                }
            });
        });

        // 6. Toggle Status
        $(document).on('click', '.btn-toggle-status', function () {
            const id = $(this).data('id');
            const btn = $(this);
            btn.prop('disabled', true);

            $.ajax({
                url: `${baseUrl}/${id}/toggle-status`,
                type: 'PATCH',
                success: function (res) {
                    showAlert('success', res.message || '{{ __("Academic monthly plan status updated successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    btn.prop('disabled', false);
                    const msg = xhr.responseJSON?.message || '{{ __("Failed to update status.") }}';
                    showAlert('danger', msg);
                }
            });
        });

        // 7. Delete Academic Monthly Plan Modal
        const deleteModalEl = document.getElementById('deleteAcademicMonthlyPlanModal');
        const deleteModal = new bootstrap.Modal(deleteModalEl);
        let targetDeleteId = null;

        $(document).on('click', '.btn-delete-monthly-plan', function () {
            targetDeleteId = $(this).data('id');
            const name = $(this).data('name') || '';
            $('#delete_target_name').text(name);
            deleteModal.show();
        });

        $('#confirm_delete_btn').on('click', function () {
            if (!targetDeleteId) return;
            const confirmBtn = $(this);
            confirmBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>');

            $.ajax({
                url: `${baseUrl}/${targetDeleteId}`,
                type: 'DELETE',
                success: function (res) {
                    confirmBtn.prop('disabled', false).text('{{ __("Yes, Delete") }}');
                    deleteModal.hide();
                    showAlert('success', res.message || '{{ __("Academic monthly plan deleted successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    confirmBtn.prop('disabled', false).text('{{ __("Yes, Delete") }}');
                    deleteModal.hide();
                    const msg = xhr.responseJSON?.message || '{{ __("Failed to delete academic monthly plan.") }}';
                    showAlert('danger', msg);
                }
            });
        });
    }

    if (document.readyState === 'complete') {
        initAcademicMonthlyPlans();
    } else {
        window.addEventListener('load', initAcademicMonthlyPlans);
    }
})();
</script>
@endsection
