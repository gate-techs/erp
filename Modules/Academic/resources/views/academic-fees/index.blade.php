@extends('layouts/contentNavbarLayout')

@section('title', __('Academic Fees') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Fees & Packages') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Academic Fees') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-money text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Academic Fees') }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddAcademicFee" aria-controls="offcanvasAddAcademicFee">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add Academic Fee') }}</span>
        </button>
    </div>
</div>

<!-- Filters Card (3 per row) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <!-- Row 1 (3 filters) -->
        <div class="row g-3 align-items-end mb-3">
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
        </div>

        <!-- Row 2 (3 filters & Reset Button) -->
        <div class="row g-3 align-items-end">
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

            <!-- 5. Fee Type Filter -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Type') }}</label>
                <select id="filter_academic_fee_type_id" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                    <option value="">{{ __('All Fee Types') }}</option>
                    @foreach ($feeTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->translate(app()->getLocale())?->name ?? $type->translate('ar')?->name ?? $type->code }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 6. Billing Period Filter & Reset -->
            <div class="col-12 col-md-4">
                <div class="row g-2 align-items-end">
                    <div class="col-8">
                        <label class="form-label fw-semibold text-heading small mb-1">{{ __('Billing Period') }}</label>
                        <select id="filter_billing_period" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                            <option value="">{{ __('All Periods') }}</option>
                            <option value="one_time">{{ __('One-Time') }}</option>
                            <option value="monthly">{{ __('Monthly') }}</option>
                            <option value="term">{{ __('Term / Semester') }}</option>
                            <option value="annually">{{ __('Annually') }}</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <button type="button" id="btn_reset_filters" class="btn btn-label-secondary w-100 d-flex align-items-center justify-content-center gap-1">
                            <i class="icon-base bx bx-reset font-18"></i>
                            <span>{{ __('Reset') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dynamic Alert Container for Ajax feedback -->
<div id="ajaxAlertContainer"></div>

<!-- Academic Fees DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="academicFeesTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Fee Name') }}</th>
                    <th>{{ __('Institution') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th>{{ __('Academic Years') }}</th>
                    <th>{{ __('Academic Stage') }}</th>
                    <th>{{ __('Fee Type') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Billing Period') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('Amount') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add Academic Fee                                            -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddAcademicFee" aria-labelledby="offcanvasAddAcademicFeeLabel" style="width: 500px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3">
        <h5 id="offcanvasAddAcademicFeeLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-plus-circle text-primary font-22"></i>
            <span>{{ __('Add Academic Fee') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form id="formAddAcademicFee" enctype="multipart/form-data">
            @csrf

            <!-- 1. Image Upload Box (Single row) -->
            <div class="mb-4 text-center">
                <label class="form-label fw-semibold text-heading small d-block text-start mb-2">{{ __('Fee Image / Icon') }}</label>
                <div class="image-upload-wrapper border rounded-3 p-3 bg-lighter d-flex flex-column align-items-center justify-content-center position-relative">
                    <img id="add_image_preview" src="{{ asset('assets/img/favicon/favicon.ico') }}" alt="Default Logo" class="rounded border object-fit-cover shadow-xs mb-2" width="72" height="72">
                    <div class="d-flex align-items-center gap-2">
                        <label for="add_fee_image" class="btn btn-outline-primary btn-sm mb-0 cursor-pointer">
                            <i class="icon-base bx bx-camera me-1"></i>
                            <span>{{ __('Upload Image') }}</span>
                            <input type="file" id="add_fee_image" name="image" class="d-none" accept="image/*" onchange="previewFeeImage(this, 'add_image_preview')">
                        </label>
                    </div>
                </div>
                <div class="invalid-feedback d-block" id="formAddAcademicFee_image_error"></div>
            </div>

            <!-- 2. Institution (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Educational Institution / Nursery') }} <span class="text-danger">*</span></label>
                <select name="institution_id" id="add_institution_id" class="select2 select2-selection__rendered form-select" required>
                    <option value="">{{ __('Select Institution...') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formAddAcademicFee_institution_id_error"></div>
            </div>

            <!-- 3. Branch (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select name="branch_id" id="add_branch_id" class="select2 select2-selection__rendered form-select">
                    <option value="">{{ __('Select Branch...') }}</option>
                </select>
                <div class="invalid-feedback d-block" id="formAddAcademicFee_branch_id_error"></div>
            </div>

            <!-- 4. Academic Year / Period (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Years') }} <span class="text-danger">*</span></label>
                <select name="academic_year_id" id="add_academic_year_id" class="select2 select2-selection__rendered form-select" required>
                    <option value="">{{ __('Select Academic Year...') }}</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->translate(app()->getLocale())?->name ?? $year->translate('ar')?->name ?? $year->name ?? $year->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formAddAcademicFee_academic_year_id_error"></div>
            </div>

            <!-- 5. Academic Stage (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Stage') }} <span class="text-danger">*</span></label>
                <select name="academic_stage_id" id="add_academic_stage_id" class="select2 select2-selection__rendered form-select" required>
                    <option value="">{{ __('Select Stage...') }}</option>
                </select>
                <div class="invalid-feedback d-block" id="formAddAcademicFee_academic_stage_id_error"></div>
            </div>

            <!-- 6. Fee Type (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Type') }} <span class="text-danger">*</span></label>
                <select name="academic_fee_type_id" id="add_academic_fee_type_id" class="select2 select2-selection__rendered form-select" required>
                    <option value="">{{ __('Select Fee Type...') }}</option>
                    @foreach ($feeTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->translate(app()->getLocale())?->name ?? $type->translate('ar')?->name ?? $type->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formAddAcademicFee_academic_fee_type_id_error"></div>
            </div>

            <!-- 7. Billing Period (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Billing Period') }} <span class="text-danger">*</span></label>
                <select name="billing_period" id="add_billing_period" class="select2 select2-selection__rendered form-select" required>
                    <option value="monthly">{{ __('Monthly') }}</option>
                    <option value="one_time">{{ __('One-Time') }}</option>
                    <option value="term">{{ __('Term / Semester') }}</option>
                    <option value="annually">{{ __('Annually') }}</option>
                </select>
                <div class="invalid-feedback d-block" id="formAddAcademicFee_billing_period_error"></div>
            </div>

            <!-- 8. Fee Name Arabic (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="add_name_ar" class="form-control" required dir="rtl">
                <div class="invalid-feedback d-block" id="formAddAcademicFee_name_ar_error"></div>
            </div>

            <!-- 9. Fee Name English (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="add_name_en" class="form-control" required dir="ltr">
                <div class="invalid-feedback d-block" id="formAddAcademicFee_name_en_error"></div>
            </div>

            <!-- 10. Fee Description Arabic (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Description [Arabic]') }}</label>
                <textarea name="description_ar" id="add_description_ar" class="form-control" rows="2" dir="rtl"></textarea>
                <div class="invalid-feedback d-block" id="formAddAcademicFee_description_ar_error"></div>
            </div>

            <!-- 11. Fee Description English (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Description [English]') }}</label>
                <textarea name="description_en" id="add_description_en" class="form-control" rows="2" dir="ltr"></textarea>
                <div class="invalid-feedback d-block" id="formAddAcademicFee_description_en_error"></div>
            </div>

            <!-- 12. Amount / Fees (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Amount') }} <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="icon-base bx bx-money"></i></span>
                    <input type="number" step="0.01" min="0" name="amount" id="add_amount" class="form-control" required>
                </div>
                <div class="invalid-feedback d-block" id="formAddAcademicFee_amount_error"></div>
            </div>

            <!-- 13. Status (Single row) -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }} <span class="text-danger">*</span></label>
                <select name="status" id="add_status" class="select2 select2-selection__rendered form-select" required>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
                <div class="invalid-feedback d-block" id="formAddAcademicFee_status_error"></div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" id="btn_submit_add" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-check font-18"></i>
                    <span>{{ __('Save') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Academic Fee                                           -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditAcademicFee" aria-labelledby="offcanvasEditAcademicFeeLabel" style="width: 500px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3">
        <h5 id="offcanvasEditAcademicFeeLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-primary font-22"></i>
            <span>{{ __('Edit Academic Fee') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form id="formEditAcademicFee" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="fee_id" id="edit_fee_id">

            <!-- 1. Image Upload Box (Single row) -->
            <div class="mb-4 text-center">
                <label class="form-label fw-semibold text-heading small d-block text-start mb-2">{{ __('Fee Image / Icon') }}</label>
                <div class="image-upload-wrapper border rounded-3 p-3 bg-lighter d-flex flex-column align-items-center justify-content-center position-relative">
                    <img id="edit_image_preview" src="{{ asset('assets/img/favicon/favicon.ico') }}" alt="Default Logo" class="rounded border object-fit-cover shadow-xs mb-2" width="72" height="72">
                    <div class="d-flex align-items-center gap-2">
                        <label for="edit_fee_image" class="btn btn-outline-primary btn-sm mb-0 cursor-pointer">
                            <i class="icon-base bx bx-camera me-1"></i>
                            <span>{{ __('Change Image') }}</span>
                            <input type="file" id="edit_fee_image" name="image" class="d-none" accept="image/*" onchange="previewFeeImage(this, 'edit_image_preview')">
                        </label>
                    </div>
                </div>
                <div class="invalid-feedback d-block" id="formEditAcademicFee_image_error"></div>
            </div>

            <!-- 2. Institution (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Educational Institution / Nursery') }} <span class="text-danger">*</span></label>
                <select name="institution_id" id="edit_institution_id" class="select2 select2-selection__rendered form-select" required>
                    <option value="">{{ __('Select Institution...') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formEditAcademicFee_institution_id_error"></div>
            </div>

            <!-- 3. Branch (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select name="branch_id" id="edit_branch_id" class="select2 select2-selection__rendered form-select">
                    <option value="">{{ __('Select Branch...') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formEditAcademicFee_branch_id_error"></div>
            </div>

            <!-- 4. Academic Year / Period (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Years') }} <span class="text-danger">*</span></label>
                <select name="academic_year_id" id="edit_academic_year_id" class="select2 select2-selection__rendered form-select" required>
                    <option value="">{{ __('Select Academic Year...') }}</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->translate(app()->getLocale())?->name ?? $year->translate('ar')?->name ?? $year->name ?? $year->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formEditAcademicFee_academic_year_id_error"></div>
            </div>

            <!-- 5. Academic Stage (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Stage') }} <span class="text-danger">*</span></label>
                <select name="academic_stage_id" id="edit_academic_stage_id" class="select2 select2-selection__rendered form-select" required>
                    <option value="">{{ __('Select Stage...') }}</option>
                    @foreach ($academicStages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->name ?? $stage->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formEditAcademicFee_academic_stage_id_error"></div>
            </div>

            <!-- 6. Fee Type (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Type') }} <span class="text-danger">*</span></label>
                <select name="academic_fee_type_id" id="edit_academic_fee_type_id" class="select2 select2-selection__rendered form-select" required>
                    <option value="">{{ __('Select Fee Type...') }}</option>
                    @foreach ($feeTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->translate(app()->getLocale())?->name ?? $type->translate('ar')?->name ?? $type->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formEditAcademicFee_academic_fee_type_id_error"></div>
            </div>

            <!-- 7. Billing Period (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Billing Period') }} <span class="text-danger">*</span></label>
                <select name="billing_period" id="edit_billing_period" class="select2 select2-selection__rendered form-select" required>
                    <option value="monthly">{{ __('Monthly') }}</option>
                    <option value="one_time">{{ __('One-Time') }}</option>
                    <option value="term">{{ __('Term / Semester') }}</option>
                    <option value="annually">{{ __('Annually') }}</option>
                </select>
                <div class="invalid-feedback d-block" id="formEditAcademicFee_billing_period_error"></div>
            </div>

            <!-- 8. Fee Name Arabic (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required dir="rtl">
                <div class="invalid-feedback d-block" id="formEditAcademicFee_name_ar_error"></div>
            </div>

            <!-- 9. Fee Name English (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="edit_name_en" class="form-control" required dir="ltr">
                <div class="invalid-feedback d-block" id="formEditAcademicFee_name_en_error"></div>
            </div>

            <!-- 10. Fee Description Arabic (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Description [Arabic]') }}</label>
                <textarea name="description_ar" id="edit_description_ar" class="form-control" rows="2" dir="rtl"></textarea>
                <div class="invalid-feedback d-block" id="formEditAcademicFee_description_ar_error"></div>
            </div>

            <!-- 11. Fee Description English (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Description [English]') }}</label>
                <textarea name="description_en" id="edit_description_en" class="form-control" rows="2" dir="ltr"></textarea>
                <div class="invalid-feedback d-block" id="formEditAcademicFee_description_en_error"></div>
            </div>

            <!-- 12. Amount / Fees (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Amount') }} <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="icon-base bx bx-money"></i></span>
                    <input type="number" step="0.01" min="0" name="amount" id="edit_amount" class="form-control" required>
                </div>
                <div class="invalid-feedback d-block" id="formEditAcademicFee_amount_error"></div>
            </div>

            <!-- 13. Status (Single row) -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }} <span class="text-danger">*</span></label>
                <select name="status" id="edit_status" class="select2 select2-selection__rendered form-select" required>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
                <div class="invalid-feedback d-block" id="formEditAcademicFee_status_error"></div>
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

<!-- ========================================================================= -->
<!-- 3. Modal: View Academic Fee Details                                       -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalViewAcademicFee" tabindex="-1" aria-labelledby="modalViewAcademicFeeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-heading d-flex align-items-center gap-2 mb-0" id="modalViewAcademicFeeLabel">
                    <i class="icon-base bx bx-money text-primary font-22"></i>
                    <span id="view_fee_title">{{ __('Academic Fee Details') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-12 col-md-4 text-center">
                        <div id="view_fee_image_wrapper" class="mb-3">
                            <img id="view_fee_image" src="" alt="Fee Image" class="w-100 rounded border object-fit-cover shadow-sm" style="max-height: 180px;">
                        </div>
                        <div class="p-3 bg-light rounded text-start">
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Institution') }}:</small> <span id="view_inst_name" class="fw-semibold text-heading">-</span></div>
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Branch') }}:</small> <span id="view_branch_name" class="fw-semibold text-heading">-</span></div>
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Academic Years') }}:</small> <span id="view_period_name" class="badge bg-label-info">-</span></div>
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Academic Stage') }}:</small> <span id="view_stage_name" class="badge bg-label-primary">-</span></div>
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Fee Type') }}:</small> <span id="view_fee_type_name" class="badge bg-label-secondary">-</span></div>
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Billing Period') }}:</small> <span id="view_billing_period" class="badge bg-label-warning">-</span></div>
                            <div><small class="text-muted d-block">{{ __('Status') }}:</small> <span id="view_fee_status" class="badge bg-label-success">-</span></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <h4 class="fw-bold text-primary mb-1" id="view_name_ar">-</h4>
                        <h6 class="text-muted mb-3" id="view_name_en">-</h6>

                        <!-- Fee Amount Highlight Card -->
                        <div class="alert alert-primary d-flex align-items-center justify-content-between p-3 mb-4 rounded-3 border-0">
                            <div class="d-flex align-items-center gap-2">
                                <i class="icon-base bx bx-money font-24"></i>
                                <span class="fw-semibold">{{ __('Total Amount') }}:</span>
                            </div>
                            <span class="fs-4 fw-bolder text-primary" id="view_amount">-</span>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-bold text-heading small mb-1">{{ __('Fee Description [Arabic]') }}:</h6>
                            <p class="text-muted bg-light p-3 rounded" id="view_description_ar" dir="rtl">-</p>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-bold text-heading small mb-1">{{ __('Fee Description [English]') }}:</h6>
                            <p class="text-muted bg-light p-3 rounded" id="view_description_en" dir="ltr">-</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 4. Modal: Delete Academic Fee Confirmation                                -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalDeleteAcademicFee" tabindex="-1" aria-labelledby="modalDeleteAcademicFeeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="icon-base bx bx-trash font-28 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Delete Academic Fee') }}</h5>
                <p class="text-muted small mb-4">{{ __('Are you sure you want to delete') }} <strong id="delete_fee_name" class="text-heading"></strong>? {{ __('This action cannot be undone.') }}</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" id="btn_confirm_delete" class="btn btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
function previewFeeImage(input, previewId) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById(previewId).src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

(function () {
    function initAcademicFees() {
        const $ = window.jQuery || window.$;
        if (!$ || !$.fn.DataTable) {
            setTimeout(initAcademicFees, 50);
            return;
        }
        if (window.__academicFeesInitialized) {
            return;
        }
        window.__academicFeesInitialized = true;

        const baseUrl = "{{ url('academic/fees') }}";
        const dataUrl = "{{ route('academic.fees.data') }}";

        // Setup CSRF header
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
                const input = $(`#${formId} [name="${field}"], #${formId} [name="${cleanField}"]`);
                input.addClass('is-invalid');
                const errorDiv = $(`#${formId}_${cleanField}_error`);
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

        // Initialize Select2 on page
        applySelect2();

        $('#offcanvasAddAcademicFee, #offcanvasEditAcademicFee').on('shown.bs.offcanvas', function () {
            applySelect2(this);
        });

        // Initialize Server-Side DataTable
        const table = $('#academicFeesTable').DataTable({
            processing: true,
            serverSide: true,
            pagingType: 'simple_numbers',
            language: {
                emptyTable: `
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="icon-base bx bx-money font-32 text-secondary"></i>
                        </div>
                        <h6 class="fw-bold mb-1">{{ __('No academic fees found') }}</h6>
                        <p class="text-muted small mb-0">{{ __('Click "Add Academic Fee" to create the first fee.') }}</p>
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
                    d.academic_fee_type_id = $('#filter_academic_fee_type_id').val();
                    d.billing_period = $('#filter_billing_period').val();
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
                { data: 'fee_info', name: 'academic_fees.id', className: 'fw-semibold text-heading' },
                { data: 'institution_name', name: 'institution_id', defaultContent: '-' },
                { data: 'branch_name', name: 'branch_id', defaultContent: '-' },
                { data: 'period_name', name: 'academic_year_id', defaultContent: '-' },
                { data: 'stage_name', name: 'academic_stage_id', defaultContent: '-' },
                { data: 'fee_type_name', name: 'academic_fee_type_id', defaultContent: '-' },
                { data: 'billing_period_badge', name: 'billing_period', className: 'text-center' },
                { data: 'amount_formatted', name: 'amount', className: 'text-center' },
                { data: 'status_badge', name: 'status', className: 'text-center' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[1, 'desc']]
        });

        // Filter Change Listeners
        $('#filter_institution_id, #filter_branch_id, #filter_academic_year_id, #filter_academic_stage_id, #filter_academic_fee_type_id, #filter_billing_period').on('change', function () {
            table.ajax.reload();
        });

        // Reset Filters Listener
        $('#btn_reset_filters').on('click', function () {
            $('#filter_institution_id').val('').trigger('change');
            $('#filter_branch_id').val('').trigger('change');
            $('#filter_academic_year_id').val('').trigger('change');
            $('#filter_academic_stage_id').val('').trigger('change');
            $('#filter_academic_fee_type_id').val('').trigger('change');
            $('#filter_billing_period').val('').trigger('change');
            table.ajax.reload();
        });

        // Dynamic Cascading for Institution -> Branches, Stages, Periods
        function loadCascadingData(instId, prefix = 'add', selectedBranch = '', selectedYear = '', selectedStage = '') {
            if (!instId) {
                $(`#${prefix}_branch_id`).html('<option value="">{{ __("Select Branch...") }}</option>').trigger('change');
                $(`#${prefix}_academic_year_id`).html('<option value="">{{ __("Select Academic Year...") }}</option>').trigger('change');
                $(`#${prefix}_academic_stage_id`).html('<option value="">{{ __("Select Stage...") }}</option>').trigger('change');
                return;
            }

            // 1. Branches
            $.get("{{ url('academic/fees-ajax/branches') }}/" + instId, function (res) {
                if (res.success) {
                    var options = '<option value="">{{ __("Select Branch...") }}</option>';
                    $.each(res.data, function (i, item) {
                        var isSelected = (String(item.id) === String(selectedBranch)) ? 'selected' : '';
                        options += '<option value="' + item.id + '" ' + isSelected + '>' + item.name + '</option>';
                    });
                    $(`#${prefix}_branch_id`).html(options).trigger('change');
                }
            });

            // 2. Periods / Academic Years
            $.get("{{ url('academic/fees-ajax/years') }}/" + instId, function (res) {
                if (res.success) {
                    var options = '<option value="">{{ __("Select Academic Year...") }}</option>';
                    $.each(res.data, function (i, item) {
                        var isSelected = (String(item.id) === String(selectedYear)) ? 'selected' : '';
                        options += '<option value="' + item.id + '" ' + isSelected + '>' + item.name + '</option>';
                    });
                    $(`#${prefix}_academic_year_id`).html(options).trigger('change');
                }
            });

            // 3. Stages
            $.get("{{ url('academic/fees-ajax/stages') }}/" + instId, function (res) {
                if (res.success) {
                    var options = '<option value="">{{ __("Select Stage...") }}</option>';
                    $.each(res.data, function (i, item) {
                        var isSelected = (String(item.id) === String(selectedStage)) ? 'selected' : '';
                        options += '<option value="' + item.id + '" ' + isSelected + '>' + item.name + '</option>';
                    });
                    $(`#${prefix}_academic_stage_id`).html(options).trigger('change');
                }
            });
        }

        $('#add_institution_id').on('change', function () {
            loadCascadingData($(this).val(), 'add');
        });

        $('#edit_institution_id').on('change', function () {
            loadCascadingData($(this).val(), 'edit');
        });

        // Submit Add Form via AJAX
        $('#formAddAcademicFee').on('submit', function (e) {
            e.preventDefault();
            clearFormErrors('formAddAcademicFee');

            const btn = $('#btn_submit_add');
            btn.prop('disabled', true).addClass('disabled');

            const formData = new FormData(this);

            $.ajax({
                url: baseUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    btn.prop('disabled', false).removeClass('disabled');
                    const offcanvasEl = document.getElementById('offcanvasAddAcademicFee');
                    const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
                    offcanvas.hide();

                    $('#formAddAcademicFee')[0].reset();
                    $('#formAddAcademicFee select').val('').trigger('change');
                    $('#add_status').val('active').trigger('change');
                    $('#add_billing_period').val('monthly').trigger('change');
                    $('#add_image_preview').attr('src', "{{ asset('assets/img/favicon/favicon.ico') }}");

                    table.ajax.reload(null, false);
                    showAlert('success', res.message || "{{ __('Academic fee created successfully.') }}");
                },
                error: function (xhr) {
                    btn.prop('disabled', false).removeClass('disabled');
                    if (xhr.status === 422) {
                        showFormErrors('formAddAcademicFee', xhr.responseJSON.errors);
                    } else {
                        showAlert('danger', xhr.responseJSON?.message || "{{ __('An error occurred while creating the fee.') }}");
                    }
                }
            });
        });

        // Open View Modal
        $(document).on('click', '.btn-view-academic-fee', function () {
            const id = $(this).data('id');
            $.get(`${baseUrl}/${id}`, function (res) {
                if (res.success) {
                    const data = res.data;
                    $('#view_fee_title').text(data.name_ar || data.name_en || "{{ __('Academic Fee Details') }}");
                    $('#view_name_ar').text(data.name_ar || '-');
                    $('#view_name_en').text(data.name_en || '-');
                    $('#view_inst_name').text(data.institution_name || '-');
                    $('#view_branch_name').text(data.branch_name || '-');
                    $('#view_period_name').text(data.academic_year_name || '-');
                    $('#view_stage_name').text(data.academic_stage_name || '-');
                    $('#view_fee_type_name').text(data.fee_type_name || '-');
                    $('#view_billing_period').text(data.billing_period_text || '-');
                    $('#view_amount').text(data.amount_formatted || '-');
                    $('#view_fee_image').attr('src', data.image_url);
                    $('#view_description_ar').text(data.description_ar || "{{ __('No description provided.') }}");
                    $('#view_description_en').text(data.description_en || "{{ __('No description provided.') }}");

                    const statusBadge = data.status === 'active'
                        ? '<span class="badge bg-label-success">{{ __("Active") }}</span>'
                        : '<span class="badge bg-label-danger">{{ __("Inactive") }}</span>';
                    $('#view_fee_status').html(statusBadge);

                    $('#modalViewAcademicFee').modal('show');
                }
            }).fail(function () {
                showAlert('danger', "{{ __('Failed to load fee details.') }}");
            });
        });

        // Open Edit Offcanvas
        $(document).on('click', '.btn-edit-academic-fee', function () {
            const id = $(this).data('id');
            clearFormErrors('formEditAcademicFee');

            $.get(`${baseUrl}/${id}`, function (res) {
                if (res.success) {
                    const data = res.data;
                    $('#edit_fee_id').val(data.id);
                    $('#edit_name_ar').val(data.name_ar);
                    $('#edit_name_en').val(data.name_en);
                    $('#edit_description_ar').val(data.description_ar);
                    $('#edit_description_en').val(data.description_en);
                    $('#edit_amount').val(data.amount);
                    $('#edit_status').val(data.status).trigger('change');
                    $('#edit_billing_period').val(data.billing_period).trigger('change');
                    $('#edit_academic_fee_type_id').val(data.academic_fee_type_id).trigger('change');
                    $('#edit_image_preview').attr('src', data.image_url);

                    // Institution & Cascading
                    $('#edit_institution_id').val(data.institution_id).trigger('change');
                    loadCascadingData(data.institution_id, 'edit', data.branch_id, data.academic_year_id, data.academic_stage_id);

                    const offcanvasEl = document.getElementById('offcanvasEditAcademicFee');
                    const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
                    offcanvas.show();
                }
            }).fail(function () {
                showAlert('danger', "{{ __('Failed to load fee details for editing.') }}");
            });
        });

        // Submit Edit Form via AJAX
        $('#formEditAcademicFee').on('submit', function (e) {
            e.preventDefault();
            clearFormErrors('formEditAcademicFee');

            const id = $('#edit_fee_id').val();
            const btn = $('#btn_submit_edit');
            btn.prop('disabled', true).addClass('disabled');

            const formData = new FormData(this);

            $.ajax({
                url: `${baseUrl}/${id}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    btn.prop('disabled', false).removeClass('disabled');
                    const offcanvasEl = document.getElementById('offcanvasEditAcademicFee');
                    const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
                    offcanvas.hide();

                    table.ajax.reload(null, false);
                    showAlert('success', res.message || "{{ __('Academic fee updated successfully.') }}");
                },
                error: function (xhr) {
                    btn.prop('disabled', false).removeClass('disabled');
                    if (xhr.status === 422) {
                        showFormErrors('formEditAcademicFee', xhr.responseJSON.errors);
                    } else {
                        showAlert('danger', xhr.responseJSON?.message || "{{ __('An error occurred while updating the fee.') }}");
                    }
                }
            });
        });

        // Toggle Status via AJAX
        $(document).on('click', '.btn-toggle-academic-fee-status', function () {
            const id = $(this).data('id');
            const $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: `${baseUrl}/${id}/toggle-status`,
                type: 'PATCH',
                success: function (res) {
                    table.ajax.reload(null, false);
                    showAlert('success', res.message || "{{ __('Status updated successfully.') }}");
                },
                error: function () {
                    $btn.prop('disabled', false);
                    showAlert('danger', "{{ __('Failed to update status.') }}");
                }
            });
        });

        // Delete Modal Trigger
        let feeIdToDelete = null;
        $(document).on('click', '.btn-delete-academic-fee', function () {
            feeIdToDelete = $(this).data('id');
            const feeName = $(this).data('name');
            $('#delete_fee_name').text(feeName);
            $('#modalDeleteAcademicFee').modal('show');
        });

        // Confirm Delete via AJAX
        $('#btn_confirm_delete').on('click', function () {
            if (!feeIdToDelete) return;

            const btn = $(this);
            btn.prop('disabled', true).addClass('disabled');

            $.ajax({
                url: `${baseUrl}/${feeIdToDelete}`,
                type: 'DELETE',
                success: function (res) {
                    btn.prop('disabled', false).removeClass('disabled');
                    $('#modalDeleteAcademicFee').modal('hide');
                    table.ajax.reload(null, false);
                    showAlert('success', res.message || "{{ __('Academic fee deleted successfully.') }}");
                    feeIdToDelete = null;
                },
                error: function () {
                    btn.prop('disabled', false).removeClass('disabled');
                    showAlert('danger', "{{ __('Failed to delete the academic fee.') }}");
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAcademicFees);
    } else {
        initAcademicFees();
    }
})();
</script>
@endsection
