@extends('layouts/contentNavbarLayout')

@section('title', __('Academic Classes') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Academic Management') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Academic Classes') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-chalkboard text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Academic Classes') }}</h4>
        </div>
    </div>

    @can('academic.classes.create')
    <div class="mt-3 mt-md-0">
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddAcademicClass" aria-controls="offcanvasAddAcademicClass">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add Academic Class') }}</span>
        </button>
    </div>
    @endcan
</div>

<!-- Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Institution Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Educational Institution / Nursery') }}</label>
                <select id="filter_institution_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Institutions') }}" data-allow-clear="true">
                    <option value="">{{ __('All Institutions') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Branch Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select id="filter_branch_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Branches') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" data-nursery-id="{{ $branch->nursery_id }}">
                            {{ $branch->translate(app()->getLocale())?->name ?? $branch->translate('ar')?->name ?? $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Academic Stage Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Stage') }}</label>
                <select id="filter_academic_stage_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Academic Stages') }}" data-allow-clear="true">
                    <option value="">{{ __('All Academic Stages') }}</option>
                    @foreach ($academicStages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->name ?? $stage->code }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Academic Year Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Year') }}</label>
                <select id="filter_academic_year_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Academic Years') }}" data-allow-clear="true">
                    <option value="">{{ __('All Academic Years') }}</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->name ?? $year->code }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 5. Status Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}">{{ __($status === 'active' ? 'Active' : 'Inactive') }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 6. Reset Button in the Same Row -->
            <div class="col-12 col-sm-6 col-lg-2">
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
        <table id="academicClassesTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Academic Class') }}</th>
                    <th>{{ __('Institution') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th>{{ __('Academic Stage') }}</th>
                    <th>{{ __('Academic Year') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Capacity') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 130px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@can('academic.classes.create')
<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add Academic Class                                          -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddAcademicClass" aria-labelledby="offcanvasAddAcademicClassLabel" style="width: 520px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddAcademicClassLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-plus-circle text-primary font-22"></i>
            <span>{{ __('Add Academic Class') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="addAcademicClassForm" action="{{ route('academic.classes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- 1. Image Upload Box with Camera Icon matching Nursery form -->
            <div class="mb-4">
                <label class="form-label fw-semibold mb-2 text-heading d-flex align-items-center justify-content-between">
                    <span>{{ __('Add New Image') }}</span>
                    <small class="text-muted">PNG, JPG, WEBP</small>
                </label>
                <div class="border rounded-3 p-3 text-center position-relative bg-lighter d-flex flex-column align-items-center justify-content-center" 
                     style="border-style: dashed !important; border-width: 2px !important; cursor: pointer; min-height: 110px;"
                     onclick="document.getElementById('add_class_image').click();">
                    
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

                    <input type="file" id="add_class_image" name="image" class="d-none" accept="image/*" onchange="previewAddImage(this)">
                </div>
                <div class="invalid-feedback d-block" id="add_image_error"></div>
            </div>

            <!-- 2. Institution -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Educational Institution / Nursery') }} <span class="text-danger">*</span></label>
                <select name="institution_id" id="add_institution_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Institution...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Institution...') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_institution_id_error"></div>
            </div>

            <!-- 3. Branch -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Branch') }} <span class="text-danger">*</span></label>
                <select name="branch_id" id="add_branch_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Branch...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Branch...') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" data-nursery-id="{{ $branch->nursery_id }}">{{ $branch->translate(app()->getLocale())?->name ?? $branch->translate('ar')?->name ?? $branch->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_branch_id_error"></div>
            </div>

            <!-- 4. Academic Stage -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Stage') }} <span class="text-danger">*</span></label>
                <select name="academic_stage_id" id="add_academic_stage_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Academic Stage...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Academic Stage...') }}</option>
                    @foreach ($academicStages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->name ?? $stage->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_academic_stage_id_error"></div>
            </div>

            <!-- 5. Academic Year -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Year') }} <span class="text-danger">*</span></label>
                <select name="academic_year_id" id="add_academic_year_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Academic Year...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Academic Year...') }}</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->name ?? $year->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_academic_year_id_error"></div>
            </div>

            <!-- 6. Class Name Arabic -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Class Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="add_name_ar" class="form-control" placeholder="{{ __('Class Name (Arabic)') }}" required maxlength="150">
                <div class="invalid-feedback d-block" id="add_name_ar_error"></div>
            </div>

            <!-- 7. Class Name English -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Class Name (English)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="add_name_en" class="form-control" placeholder="{{ __('Class Name (English)') }}" required maxlength="150" dir="ltr">
                <div class="invalid-feedback d-block" id="add_name_en_error"></div>
            </div>

            <!-- 8. Capacity, Color, Status in 3 columns -->
            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-4">
                    <label class="form-label fw-semibold text-heading">{{ __('Class Capacity') }}</label>
                    <input type="number" name="capacity" id="add_capacity" class="form-control" placeholder="25" min="0" step="1">
                    <div class="invalid-feedback d-block" id="add_capacity_error"></div>
                </div>

                <div class="col-12 col-sm-4">
                    <label class="form-label fw-semibold text-heading">{{ __('Color') }}</label>
                    <input type="color" name="color" id="add_color" class="form-control form-control-color w-100" value="#696cff" style="height: 38px;">
                    <div class="invalid-feedback d-block" id="add_color_error"></div>
                </div>

                <div class="col-12 col-sm-4">
                    <label class="form-label fw-semibold text-heading">{{ __('Status') }}</label>
                    <select name="status" id="add_status" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('Select Status') }}">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" {{ $status === 'active' ? 'selected' : '' }}>{{ __($status === 'active' ? 'Active' : 'Inactive') }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback d-block" id="add_status_error"></div>
                </div>
            </div>

            <!-- 9. Description Arabic -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" id="add_description_ar" class="form-control" rows="2" placeholder="{{ __('Description (Arabic)') }}"></textarea>
                <div class="invalid-feedback d-block" id="add_description_ar_error"></div>
            </div>

            <!-- 10. Description English -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading">{{ __('Description (English)') }}</label>
                <textarea name="description_en" id="add_description_en" class="form-control" rows="2" placeholder="{{ __('Description (English)') }}" dir="ltr"></textarea>
                <div class="invalid-feedback d-block" id="add_description_en_error"></div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" id="btn_submit_add" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-save font-18"></i>
                    <span>{{ __('Save Academic Class') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@can('academic.classes.edit')
<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Academic Class                                         -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditAcademicClass" aria-labelledby="offcanvasEditAcademicClassLabel" style="width: 520px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditAcademicClassLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-primary font-22"></i>
            <span>{{ __('Edit Academic Class') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="editAcademicClassForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- 1. Image Upload Box with Camera Icon matching Nursery form -->
            <div class="mb-4">
                <label class="form-label fw-semibold mb-2 text-heading d-flex align-items-center justify-content-between">
                    <span>{{ __('Add New Image') }}</span>
                    <small class="text-muted">PNG, JPG, WEBP</small>
                </label>
                <div class="border rounded-3 p-3 text-center position-relative bg-lighter d-flex flex-column align-items-center justify-content-center" 
                     style="border-style: dashed !important; border-width: 2px !important; cursor: pointer; min-height: 110px;"
                     onclick="document.getElementById('edit_class_image').click();">
                    
                    <div id="edit_image_placeholder" class="d-flex align-items-center justify-content-between w-100 px-2">
                        <span class="text-muted small">{{ __('Add New Image') }}</span>
                        <i class="icon-base bx bx-camera text-primary font-24"></i>
                    </div>

                    <div id="edit_image_preview_box" class="d-none align-items-center gap-2 w-100">
                        <img id="edit_image_preview" src="" alt="Preview" class="rounded object-fit-cover border" width="56" height="56">
                        <div class="text-start">
                            <span id="edit_image_name" class="fw-semibold small d-block text-truncate" style="max-width: 220px;"></span>
                            <small class="text-primary">{{ __('Click to change image') }}</small>
                        </div>
                    </div>

                    <input type="file" id="edit_class_image" name="image" class="d-none" accept="image/*" onchange="previewEditImage(this)">
                </div>
                <div class="invalid-feedback d-block" id="edit_image_error"></div>
            </div>

            <!-- 2. Institution -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Educational Institution / Nursery') }} <span class="text-danger">*</span></label>
                <select name="institution_id" id="edit_institution_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Institution...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Institution...') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_institution_id_error"></div>
            </div>

            <!-- 3. Branch -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Branch') }} <span class="text-danger">*</span></label>
                <select name="branch_id" id="edit_branch_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Branch...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Branch...') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" data-nursery-id="{{ $branch->nursery_id }}">{{ $branch->translate(app()->getLocale())?->name ?? $branch->translate('ar')?->name ?? $branch->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_branch_id_error"></div>
            </div>

            <!-- 4. Academic Stage -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Stage') }} <span class="text-danger">*</span></label>
                <select name="academic_stage_id" id="edit_academic_stage_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Academic Stage...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Academic Stage...') }}</option>
                    @foreach ($academicStages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->name ?? $stage->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_academic_stage_id_error"></div>
            </div>

            <!-- 5. Academic Year -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Year') }} <span class="text-danger">*</span></label>
                <select name="academic_year_id" id="edit_academic_year_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Academic Year...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Academic Year...') }}</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->name ?? $year->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_academic_year_id_error"></div>
            </div>

            <!-- 6. Class Name Arabic -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Class Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="edit_name_ar" class="form-control" placeholder="{{ __('Class Name (Arabic)') }}" required maxlength="150">
                <div class="invalid-feedback d-block" id="edit_name_ar_error"></div>
            </div>

            <!-- 7. Class Name English -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Class Name (English)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="edit_name_en" class="form-control" placeholder="{{ __('Class Name (English)') }}" required maxlength="150" dir="ltr">
                <div class="invalid-feedback d-block" id="edit_name_en_error"></div>
            </div>

            <!-- 8. Capacity, Color, Status in 3 columns -->
            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-4">
                    <label class="form-label fw-semibold text-heading">{{ __('Class Capacity') }}</label>
                    <input type="number" name="capacity" id="edit_capacity" class="form-control" placeholder="25" min="0" step="1">
                    <div class="invalid-feedback d-block" id="edit_capacity_error"></div>
                </div>

                <div class="col-12 col-sm-4">
                    <label class="form-label fw-semibold text-heading">{{ __('Color') }}</label>
                    <input type="color" name="color" id="edit_color" class="form-control form-control-color w-100" value="#696cff" style="height: 38px;">
                    <div class="invalid-feedback d-block" id="edit_color_error"></div>
                </div>

                <div class="col-12 col-sm-4">
                    <label class="form-label fw-semibold text-heading">{{ __('Status') }}</label>
                    <select name="status" id="edit_status" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('Select Status') }}">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}">{{ __($status === 'active' ? 'Active' : 'Inactive') }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback d-block" id="edit_status_error"></div>
                </div>
            </div>

            <!-- 9. Description Arabic -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" id="edit_description_ar" class="form-control" rows="2" placeholder="{{ __('Description (Arabic)') }}"></textarea>
                <div class="invalid-feedback d-block" id="edit_description_ar_error"></div>
            </div>

            <!-- 10. Description English -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading">{{ __('Description (English)') }}</label>
                <textarea name="description_en" id="edit_description_en" class="form-control" rows="2" placeholder="{{ __('Description (English)') }}" dir="ltr"></textarea>
                <div class="invalid-feedback d-block" id="edit_description_en_error"></div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" id="btn_submit_edit" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-save font-18"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@can('academic.classes.delete')
<!-- ========================================================================= -->
<!-- 3. Modal: Delete Academic Class Confirmation                             -->
<!-- ========================================================================= -->
<div class="modal fade" id="deleteAcademicClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pt-0 pb-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="icon-base bx bx-trash font-28 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted small mb-4">
                    {{ __('Are you sure you want to delete the academic class') }} <strong id="delete_target_name" class="text-heading"></strong>?
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" id="confirm_delete_btn" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan

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
            document.getElementById('edit_image_placeholder').classList.add('d-none');
            document.getElementById('edit_image_preview_box').classList.remove('d-none');
            document.getElementById('edit_image_preview_box').classList.add('d-flex');
        };
        reader.readAsDataURL(file);
    }
}

(function () {
    function initAcademicClasses() {
        const $ = window.jQuery || window.$;
        if (!$ || !$.fn.DataTable) {
            setTimeout(initAcademicClasses, 50);
            return;
        }

        const baseUrl = "{{ url('academic/classes') }}";
        const dataUrl = "{{ route('academic.classes.data') }}";

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
            const input = $(`#${formId} [name="${field}"]`);
            input.addClass('is-invalid');
            const errorDiv = $(`#${formId}_${field}_error`) || $(`#${formId} #${formId}_${field}_error`);
            if (errorDiv.length) {
                errorDiv.text(errors[field][0]);
            } else {
                input.closest('.mb-3, .col-12, .col-sm-6').find('.invalid-feedback').text(errors[field][0]);
            }
        }
    }

    // Initialize Select2 matching standard ERP pattern
    if (typeof window.initSelect2 === 'function') {
        window.initSelect2();
    } else {
        const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';
        $('.select2, .select2-selection__rendered').each(function () {
            const $this = $(this);
            if ($this.hasClass('select2-hidden-accessible')) return;
            const $parent = $this.closest('.offcanvas, .modal');
            $this.select2({
                theme: 'bootstrap-5',
                dropdownParent: $parent.length ? $parent : null,
                placeholder: $this.data('placeholder') || '',
                allowClear: $this.data('allow-clear') !== false,
                dir: isRtl ? 'rtl' : 'ltr',
                width: '100%'
            });
        });
    }

    // Offcanvas Select2 fix on shown
    $('#offcanvasAddAcademicClass, #offcanvasEditAcademicClass').on('shown.bs.offcanvas', function () {
        const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';
        $(this).find('select.select2, select.select2-selection__rendered').each(function () {
            const $this = $(this);
            if (!$this.hasClass('select2-hidden-accessible')) {
                $this.select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $this.closest('.offcanvas'),
                    placeholder: $this.data('placeholder') || '',
                    allowClear: $this.data('allow-clear') !== false,
                    dir: isRtl ? 'rtl' : 'ltr',
                    width: '100%'
                });
            }
        });
    });

    // Initialize Server-Side DataTable matching the theme style
    const table = $('#academicClassesTable').DataTable({
        processing: true,
        serverSide: true,
        pagingType: 'simple_numbers',
        language: {
            emptyTable: `
                <div class="text-center py-5">
                    <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                        <i class="icon-base bx bx-chalkboard font-32 text-secondary"></i>
                    </div>
                    <h6 class="fw-bold mb-1">{{ __('No academic classes found') }}</h6>
                    <p class="text-muted small mb-0">{{ __('Click "Add Academic Class" to create the first class.') }}</p>
                </div>
            `
        },
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.institution_id = $('#filter_institution_id').val();
                d.branch_id = $('#filter_branch_id').val();
                d.academic_stage_id = $('#filter_academic_stage_id').val();
                d.academic_year_id = $('#filter_academic_year_id').val();
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
            { data: 'name', name: 'translations.name', className: 'fw-semibold text-heading' },
            { data: 'institution_name', name: 'institution_id', defaultContent: '-' },
            { data: 'branch_name', name: 'branch_id', defaultContent: '-' },
            { data: 'stage_name', name: 'academic_stage_id', defaultContent: '-' },
            { data: 'year_name', name: 'academic_year_id', defaultContent: '-' },
            { data: 'capacity_text', name: 'capacity', className: 'text-center' },
            { data: 'status_badge', name: 'status', className: 'text-center' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']]
    });

    // Filter Change Listeners
    $('#filter_institution_id, #filter_branch_id, #filter_academic_stage_id, #filter_academic_year_id, #filter_status').on('change', function () {
        table.ajax.reload();
    });

    // Reset Filters Listener (All in same row)
    $('#btn_reset_filters').on('click', function () {
        $('#filter_institution_id').val('').trigger('change');
        $('#filter_branch_id').val('').trigger('change');
        $('#filter_academic_stage_id').val('').trigger('change');
        $('#filter_academic_year_id').val('').trigger('change');
        $('#filter_status').val('').trigger('change');
        table.ajax.reload();
    });

    // 1. Submit Add Academic Class
    $('#addAcademicClassForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#btn_submit_add');
        const originalBtnHtml = submitBtn.html();

        clearFormErrors('addAcademicClassForm');
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
                const offcanvasEl = document.getElementById('offcanvasAddAcademicClass');
                const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                if (bsOffcanvas) bsOffcanvas.hide();

                form[0].reset();
                $('#add_name_en').data('manually_typed', false);
                form.find('select.select2, select.select2-selection__rendered').val('').trigger('change');
                $('#add_status').val('active').trigger('change');
                $('#add_color').val('#696cff');
                $('#add_class_image').val('');
                $('#add_image_preview').attr('src', '');
                $('#add_image_name').text('');
                $('#add_image_preview_box').removeClass('d-flex').addClass('d-none');
                $('#add_image_placeholder').removeClass('d-none');

                showAlert('success', res.message || '{{ __("Academic class created successfully.") }}');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showFormErrors('addAcademicClassForm', xhr.responseJSON.errors);
                } else {
                    const msg = xhr.responseJSON?.message || '{{ __("Error saving academic class.") }}';
                    showAlert('danger', msg);
                }
            }
        });
    });

    // Sync Arabic & English names automatically
    $('#add_name_ar').on('input', function () {
        if (!$('#add_name_en').data('manually_typed')) {
            $('#add_name_en').val($(this).val());
        }
    });
    $('#add_name_en').on('input', function () {
        $(this).data('manually_typed', true);
    });

    $('#edit_name_ar').on('input', function () {
        if (!$('#edit_name_en').data('manually_typed')) {
            $('#edit_name_en').val($(this).val());
        }
    });
    $('#edit_name_en').on('input', function () {
        $(this).data('manually_typed', true);
    });

    // 2. Open Edit Offcanvas & Fetch Data
    const editOffcanvasEl = document.getElementById('offcanvasEditAcademicClass');
    const editOffcanvas = new bootstrap.Offcanvas(editOffcanvasEl);

    $(document).on('click', '.btn-edit-academic-class', function () {
        const id = $(this).data('id');
        clearFormErrors('editAcademicClassForm');
        $('#edit_name_en').data('manually_typed', false);

        $.ajax({
            url: `${baseUrl}/${id}`,
            type: 'GET',
            success: function (res) {
                if (res.success && res.data) {
                    const item = res.data;

                    $('#edit_institution_id').val(item.institution_id || '').trigger('change');
                    $('#edit_branch_id').val(item.branch_id || '').trigger('change');
                    $('#edit_academic_stage_id').val(item.academic_stage_id || '').trigger('change');
                    $('#edit_academic_year_id').val(item.academic_year_id || '').trigger('change');

                    $('#edit_name_ar').val(item.name_ar || (item.translations && item.translations.find(t => t.locale === 'ar')?.name) || '');
                    $('#edit_name_en').val(item.name_en || (item.translations && item.translations.find(t => t.locale === 'en')?.name) || '');
                    $('#edit_capacity').val(item.capacity || '');
                    $('#edit_color').val(item.color || '#696cff');
                    $('#edit_status').val(item.status || 'active').trigger('change');
                    $('#edit_class_image').val('');

                    // Populate image preview
                    const editPreviewImg = document.getElementById('edit_image_preview');
                    const editPreviewName = document.getElementById('edit_image_name');
                    const editPreviewBox = document.getElementById('edit_image_preview_box');
                    const editPlaceholder = document.getElementById('edit_image_placeholder');

                    if (item.image_url) {
                        editPreviewImg.src = item.image_url;
                        editPreviewName.textContent = '{{ __("Current Image") }}';
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

                    $('#edit_description_ar').val(item.description_ar || (item.translations && item.translations.find(t => t.locale === 'ar')?.description) || '');
                    $('#edit_description_en').val(item.description_en || (item.translations && item.translations.find(t => t.locale === 'en')?.description) || '');

                    $('#editAcademicClassForm').attr('action', `${baseUrl}/${item.id}`);
                    editOffcanvas.show();
                }
            },
            error: function () {
                showAlert('danger', '{{ __("Failed to load academic class data.") }}');
            }
        });
    });

    // 3. Submit Edit Academic Class
    $('#editAcademicClassForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#btn_submit_edit');
        const originalBtnHtml = submitBtn.html();
        const actionUrl = form.attr('action');

        clearFormErrors('editAcademicClassForm');
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
                showAlert('success', res.message || '{{ __("Academic class updated successfully.") }}');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showFormErrors('editAcademicClassForm', xhr.responseJSON.errors);
                } else {
                    const msg = xhr.responseJSON?.message || '{{ __("Error updating academic class.") }}';
                    showAlert('danger', msg);
                }
            }
        });
    });

    // 4. Toggle Status
    $(document).on('click', '.btn-toggle-academic-class-status', function () {
        const id = $(this).data('id');
        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: `${baseUrl}/${id}/toggle-status`,
            type: 'PATCH',
            success: function (res) {
                showAlert('success', res.message || '{{ __("Academic class status updated successfully.") }}');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                const msg = xhr.responseJSON?.message || '{{ __("Failed to update status.") }}';
                showAlert('danger', msg);
            }
        });
    });

    // 5. Delete Academic Class Modal
    const deleteModalEl = document.getElementById('deleteAcademicClassModal');
    const deleteModal = new bootstrap.Modal(deleteModalEl);
    let targetDeleteId = null;

    $(document).on('click', '.btn-delete-academic-class', function () {
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
                showAlert('success', res.message || '{{ __("Academic class deleted successfully.") }}');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                confirmBtn.prop('disabled', false).text('{{ __("Yes, Delete") }}');
                deleteModal.hide();
                const msg = xhr.responseJSON?.message || '{{ __("Failed to delete academic class.") }}';
                showAlert('danger', msg);
            }
        });
    });
    }

    if (document.readyState === 'complete') {
        initAcademicClasses();
    } else {
        window.addEventListener('load', initAcademicClasses);
    }
})();
</script>
@endsection
