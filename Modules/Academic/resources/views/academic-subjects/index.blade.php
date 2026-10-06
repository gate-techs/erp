@extends('layouts/contentNavbarLayout')

@section('title', __('Academic Subjects') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Academic Management') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Academic Subjects') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-book text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Academic Subjects') }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <a href="{{ route('academic.subjects.board') }}" class="btn btn-label-info btn-sm d-flex align-items-center gap-2 shadow-sm" title="{{ __('Drag & Drop Board View') }}">
            <i class="icon-base bx bx-grid-alt font-18"></i>
            <span>{{ __('Stages Board (Drag & Drop)') }}</span>
        </a>
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddAcademicSubject" aria-controls="offcanvasAddAcademicSubject">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add Academic Subject') }}</span>
        </button>
    </div>
</div>

<!-- Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Institution Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Educational Institution / Nursery') }}</label>
                <select id="filter_institution_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Institutions') }}" data-allow-clear="true">
                    <option value="">{{ __('All Institutions') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Academic Stage Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Stage') }}</label>
                <select id="filter_academic_stage_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Academic Stages') }}" data-allow-clear="true">
                    <option value="">{{ __('All Academic Stages') }}</option>
                    @foreach ($academicStages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->name ?? $stage->code }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Academic Year Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Year') }}</label>
                <select id="filter_academic_year_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Academic Years') }}" data-allow-clear="true">
                    <option value="">{{ __('All Academic Years') }}</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->translate(app()->getLocale())?->name ?? $year->translate('ar')?->name ?? $year->name ?? $year->code }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Status Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}">{{ __($status === 'active' ? 'Active' : 'Inactive') }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 5. Reset Button in the Same Row -->
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
        <table id="academicSubjectsTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Subject') }}</th>
                    <th>{{ __('Institution') }}</th>
                    <th>{{ __('Academic Stages') }}</th>
                    <th>{{ __('Academic Year') }}</th>
                    <th class="text-center" style="width: 90px;">{{ __('Attachments') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add Academic Subject                                        -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddAcademicSubject" aria-labelledby="offcanvasAddAcademicSubjectLabel" style="width: 540px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddAcademicSubjectLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-plus-circle text-primary font-22"></i>
            <span>{{ __('Add Academic Subject') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="addAcademicSubjectForm" action="{{ route('academic.subjects.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- 1. Image Upload Box with Camera Icon matching Nursery form -->
            <div class="mb-4">
                <label class="form-label fw-semibold mb-2 text-heading d-flex align-items-center justify-content-between">
                    <span>{{ __('Add New Image') }}</span>
                    <small class="text-muted">PNG, JPG, WEBP</small>
                </label>
                <div class="border rounded-3 p-3 text-center position-relative bg-lighter d-flex flex-column align-items-center justify-content-center" 
                     style="border-style: dashed !important; border-width: 2px !important; cursor: pointer; min-height: 110px;"
                     onclick="document.getElementById('add_subject_image').click();">
                    
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

                    <input type="file" id="add_subject_image" name="image" class="d-none" accept="image/*" onchange="previewAddImage(this)">
                </div>
                <div class="invalid-feedback d-block" id="add_image_error"></div>
            </div>

            <!-- 2. Institution -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Educational Institution / Nursery') }} <span class="text-danger">*</span></label>
                <select name="institution_id" id="add_institution_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Institution...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Institution...') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_institution_id_error"></div>
            </div>

            <!-- 3. Academic Stages (Multi-Select) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Stages') }} <span class="text-danger">*</span></label>
                <select name="academic_stages[]" id="add_academic_stages" class="select2 select2-selection__rendered form-select" multiple="multiple" required data-placeholder="{{ __('Select Academic Stages...') }}">
                    @foreach ($academicStages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->name ?? $stage->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_academic_stages_error"></div>
            </div>

            <!-- 4. Academic Year / Term -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Academic Year / Term') }}</label>
                <select name="academic_year_id" id="add_academic_year_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('Select Academic Year...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Academic Year...') }}</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->translate(app()->getLocale())?->name ?? $year->translate('ar')?->name ?? $year->name ?? $year->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_academic_year_id_error"></div>
            </div>

            <!-- 5. Subject Name Arabic -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Subject Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="add_name_ar" class="form-control" placeholder="{{ __('Subject Name (Arabic)') }}" required maxlength="150">
                <div class="invalid-feedback d-block" id="add_name_ar_error"></div>
            </div>

            <!-- 6. Subject Name English -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Subject Name (English)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="add_name_en" class="form-control" placeholder="{{ __('Subject Name (English)') }}" required maxlength="150" dir="ltr">
                <div class="invalid-feedback d-block" id="add_name_en_error"></div>
            </div>

            <!-- 7. Status -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Status') }}</label>
                <select name="status" id="add_status" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('Select Status') }}">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" {{ $status === 'active' ? 'selected' : '' }}>{{ __($status === 'active' ? 'Active' : 'Inactive') }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_status_error"></div>
            </div>

            <!-- 8. Description Arabic -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" id="add_description_ar" class="form-control" rows="2" placeholder="{{ __('Description (Arabic)') }}"></textarea>
                <div class="invalid-feedback d-block" id="add_description_ar_error"></div>
            </div>

            <!-- 9. Description English -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (English)') }}</label>
                <textarea name="description_en" id="add_description_en" class="form-control" rows="2" placeholder="{{ __('Description (English)') }}" dir="ltr"></textarea>
                <div class="invalid-feedback d-block" id="add_description_en_error"></div>
            </div>

            <!-- 10. Multi-file Attachments -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between">
                    <span><i class="icon-base bx bx-paperclip me-1"></i>{{ __('Attachments / Curriculum Files') }}</span>
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
                    <span>{{ __('Save Academic Subject') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Academic Subject                                       -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditAcademicSubject" aria-labelledby="offcanvasEditAcademicSubjectLabel" style="width: 540px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditAcademicSubjectLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-primary font-22"></i>
            <span>{{ __('Edit Academic Subject') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="editAcademicSubjectForm" method="POST" enctype="multipart/form-data">
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
                     onclick="document.getElementById('edit_subject_image').click();">
                    
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

                    <input type="file" id="edit_subject_image" name="image" class="d-none" accept="image/*" onchange="previewEditImage(this)">
                </div>
                <div class="invalid-feedback d-block" id="edit_image_error"></div>
            </div>

            <!-- 2. Institution -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Educational Institution / Nursery') }} <span class="text-danger">*</span></label>
                <select name="institution_id" id="edit_institution_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Institution...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Institution...') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_institution_id_error"></div>
            </div>

            <!-- 3. Academic Stages (Multi-Select) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Stages') }} <span class="text-danger">*</span></label>
                <select name="academic_stages[]" id="edit_academic_stages" class="select2 select2-selection__rendered form-select" multiple="multiple" required data-placeholder="{{ __('Select Academic Stages...') }}">
                    @foreach ($academicStages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->name ?? $stage->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_academic_stages_error"></div>
            </div>

            <!-- 4. Academic Year / Term -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Academic Year / Term') }}</label>
                <select name="academic_year_id" id="edit_academic_year_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('Select Academic Year...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Academic Year...') }}</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->translate(app()->getLocale())?->name ?? $year->translate('ar')?->name ?? $year->name ?? $year->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_academic_year_id_error"></div>
            </div>

            <!-- 5. Subject Name Arabic -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Subject Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="edit_name_ar" class="form-control" placeholder="{{ __('Subject Name (Arabic)') }}" required maxlength="150">
                <div class="invalid-feedback d-block" id="edit_name_ar_error"></div>
            </div>

            <!-- 6. Subject Name English -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Subject Name (English)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="edit_name_en" class="form-control" placeholder="{{ __('Subject Name (English)') }}" required maxlength="150" dir="ltr">
                <div class="invalid-feedback d-block" id="edit_name_en_error"></div>
            </div>

            <!-- 7. Status -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Status') }}</label>
                <select name="status" id="edit_status" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('Select Status') }}">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}">{{ __($status === 'active' ? 'Active' : 'Inactive') }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_status_error"></div>
            </div>

            <!-- 8. Description Arabic -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" id="edit_description_ar" class="form-control" rows="2" placeholder="{{ __('Description (Arabic)') }}"></textarea>
                <div class="invalid-feedback d-block" id="edit_description_ar_error"></div>
            </div>

            <!-- 9. Description English -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (English)') }}</label>
                <textarea name="description_en" id="edit_description_en" class="form-control" rows="2" placeholder="{{ __('Description (English)') }}" dir="ltr"></textarea>
                <div class="invalid-feedback d-block" id="edit_description_en_error"></div>
            </div>

            <!-- 10. Existing Attachments List -->
            <div class="mb-3" id="edit_existing_attachments_container" style="display: none;">
                <label class="form-label fw-semibold text-heading mb-2">{{ __('Current Attachments') }}</label>
                <div class="list-group list-group-flush border rounded-3" id="edit_existing_attachments_list"></div>
            </div>

            <!-- 11. Add More Attachments -->
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

<!-- ========================================================================= -->
<!-- 3. Modal: Confirm Delete                                                  -->
<!-- ========================================================================= -->
<div class="modal fade" id="deleteAcademicSubjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pt-0 pb-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="icon-base bx bx-trash font-28 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted small mb-4">
                    {{ __('Are you sure you want to delete the academic subject') }} <strong id="delete_target_name" class="text-heading"></strong>?
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" id="confirm_delete_btn" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 4. Modal: Confirm Clone                                                   -->
<!-- ========================================================================= -->
<div class="modal fade" id="cloneAcademicSubjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pt-0 pb-4">
                <div class="avatar avatar-xl bg-label-info mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="icon-base bx bx-copy font-28 text-info"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Clone Academic Subject') }}</h5>
                <p class="text-muted small mb-4">
                    {{ __('Are you sure you want to create a clone of') }} <strong id="clone_target_name" class="text-heading"></strong>?
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" id="confirm_clone_btn" class="btn btn-info text-white">{{ __('Yes, Clone') }}</button>
                </div>
            </div>
        </div>
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
            document.getElementById('edit_image_placeholder').classList.add('d-none');
            document.getElementById('edit_image_preview_box').classList.remove('d-none');
            document.getElementById('edit_image_preview_box').classList.add('d-flex');
        };
        reader.readAsDataURL(file);
    }
}

(function () {
    function initAcademicSubjects() {
        const $ = window.jQuery || window.$;
        if (!$ || !$.fn.DataTable) {
            setTimeout(initAcademicSubjects, 50);
            return;
        }

        const baseUrl = "{{ url('academic/subjects') }}";
        const dataUrl = "{{ route('academic.subjects.data') }}";

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
                    input.closest('.mb-3, .mb-4, .col-12, .col-sm-4').find('.invalid-feedback').text(errors[field][0]);
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
        $('#offcanvasAddAcademicSubject, #offcanvasEditAcademicSubject').on('shown.bs.offcanvas', function () {
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
        const table = $('#academicSubjectsTable').DataTable({
            processing: true,
            serverSide: true,
            pagingType: 'simple_numbers',
            language: {
                emptyTable: `
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="icon-base bx bx-book font-32 text-secondary"></i>
                        </div>
                        <h6 class="fw-bold mb-1">{{ __('No academic subjects found') }}</h6>
                        <p class="text-muted small mb-0">{{ __('Click "Add Academic Subject" to create the first subject.') }}</p>
                    </div>
                `
            },
            ajax: {
                url: dataUrl,
                data: function (d) {
                    d.institution_id = $('#filter_institution_id').val();
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
                { data: 'stages_badges', name: 'academic_stages', orderable: false, searchable: false },
                { data: 'year_name', name: 'academic_year_id', defaultContent: '-' },
                { data: 'attachments_count_badge', name: 'attachments_count', className: 'text-center', orderable: false, searchable: false },
                { data: 'status_badge', name: 'status', className: 'text-center' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[0, 'desc']]
        });

        // Filter Change Listeners
        $('#filter_institution_id, #filter_academic_stage_id, #filter_academic_year_id, #filter_status').on('change', function () {
            table.ajax.reload();
        });

        // Reset Filters Listener (All in same row)
        $('#btn_reset_filters').on('click', function () {
            $('#filter_institution_id').val('').trigger('change');
            $('#filter_academic_stage_id').val('').trigger('change');
            $('#filter_academic_year_id').val('').trigger('change');
            $('#filter_status').val('').trigger('change');
            table.ajax.reload();
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

        // 1. Submit Add Academic Subject
        $('#addAcademicSubjectForm').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = $('#btn_submit_add');
            const originalBtnHtml = submitBtn.html();

            clearFormErrors('addAcademicSubjectForm');
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
                    const offcanvasEl = document.getElementById('offcanvasAddAcademicSubject');
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                    if (bsOffcanvas) bsOffcanvas.hide();

                    form[0].reset();
                    $('#add_name_en').data('manually_typed', false);
                    form.find('select.select2, select.select2-selection__rendered').val('').trigger('change');
                    $('#add_status').val('active').trigger('change');
                    $('#add_subject_image').val('');
                    $('#add_image_preview').attr('src', '');
                    $('#add_image_name').text('');
                    $('#add_image_preview_box').removeClass('d-flex').addClass('d-none');
                    $('#add_image_placeholder').removeClass('d-none');

                    showAlert('success', res.message || '{{ __("Academic subject created successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        showFormErrors('addAcademicSubjectForm', xhr.responseJSON.errors);
                    } else {
                        const msg = xhr.responseJSON?.message || '{{ __("Error saving academic subject.") }}';
                        showAlert('danger', msg);
                    }
                }
            });
        });

        // 2. Open Edit Offcanvas & Fetch Data
        const editOffcanvasEl = document.getElementById('offcanvasEditAcademicSubject');
        const editOffcanvas = new bootstrap.Offcanvas(editOffcanvasEl);

        $(document).on('click', '.btn-edit-academic-subject', function () {
            const id = $(this).data('id');
            clearFormErrors('editAcademicSubjectForm');
            $('#edit_name_en').data('manually_typed', false);

            $.ajax({
                url: `${baseUrl}/${id}`,
                type: 'GET',
                success: function (res) {
                    if (res.success && res.data) {
                        const item = res.data;

                        $('#edit_institution_id').val(item.institution_id ? String(item.institution_id) : '').trigger('change');
                        const stageIds = (item.stage_ids || []).map(function (s) { return String(s); });
                        $('#edit_academic_stages').val(stageIds).trigger('change');
                        $('#edit_academic_year_id').val(item.academic_year_id ? String(item.academic_year_id) : '').trigger('change');

                        $('#edit_name_ar').val(item.name_ar || (item.translations && item.translations.find(t => t.locale === 'ar')?.name) || '');
                        $('#edit_name_en').val(item.name_en || (item.translations && item.translations.find(t => t.locale === 'en')?.name) || '');
                        $('#edit_status').val(item.status || 'active').trigger('change');
                        $('#edit_subject_image').val('');
                        $('#edit_attachments').val('');

                        // Image Preview
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

                        // Existing Attachments List
                        const $attachContainer = $('#edit_existing_attachments_container');
                        const $attachList = $('#edit_existing_attachments_list');
                        $attachList.empty();

                        if (item.attachments && item.attachments.length > 0) {
                            item.attachments.forEach(att => {
                                const row = `
                                    <div class="list-group-item d-flex align-items-center justify-content-between py-2 px-3" id="attachment_row_${att.id}">
                                        <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 320px;">
                                            <i class="icon-base bx bx-file text-primary font-20 flex-shrink-0"></i>
                                            <span class="small fw-semibold text-truncate">${att.file_name}</span>
                                            <small class="text-muted">(${att.formatted_size || ''})</small>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <a href="${baseUrl}/attachments/${att.id}/download" class="btn btn-icon btn-xs btn-label-primary" title="{{ __('Download') }}">
                                                <i class="icon-base bx bx-download"></i>
                                            </a>
                                            <button type="button" class="btn btn-icon btn-xs btn-label-danger btn-delete-subject-attachment" data-id="${att.id}" title="{{ __('Delete') }}">
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

                        $('#edit_description_ar').val(item.description_ar || (item.translations && item.translations.find(t => t.locale === 'ar')?.description) || '');
                        $('#edit_description_en').val(item.description_en || (item.translations && item.translations.find(t => t.locale === 'en')?.description) || '');

                        $('#editAcademicSubjectForm').attr('action', `${baseUrl}/${item.id}`);
                        editOffcanvas.show();
                    }
                },
                error: function () {
                    showAlert('danger', '{{ __("Failed to load academic subject data.") }}');
                }
            });
        });

        // 3. Submit Edit Academic Subject
        $('#editAcademicSubjectForm').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = $('#btn_submit_edit');
            const originalBtnHtml = submitBtn.html();
            const actionUrl = form.attr('action');

            clearFormErrors('editAcademicSubjectForm');
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
                    showAlert('success', res.message || '{{ __("Academic subject updated successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        showFormErrors('editAcademicSubjectForm', xhr.responseJSON.errors);
                    } else {
                        const msg = xhr.responseJSON?.message || '{{ __("Error updating academic subject.") }}';
                        showAlert('danger', msg);
                    }
                }
            });
        });

        // 4. Delete Attachment (Ajax)
        $(document).on('click', '.btn-delete-subject-attachment', function () {
            const attId = $(this).data('id');
            const btn = $(this);
            btn.prop('disabled', true);

            $.ajax({
                url: `${baseUrl}/attachments/${attId}`,
                type: 'DELETE',
                success: function (res) {
                    $(`#attachment_row_${attId}`).fadeOut(300, function () {
                        $(this).remove();
                        if ($('#edit_existing_attachments_list').children().length === 0) {
                            $('#edit_existing_attachments_container').hide();
                        }
                    });
                    showAlert('success', res.message || '{{ __("Attachment deleted successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function () {
                    btn.prop('disabled', false);
                    showAlert('danger', '{{ __("Failed to delete attachment.") }}');
                }
            });
        });

        // 5. Toggle Status
        $(document).on('click', '.btn-toggle-academic-subject-status', function () {
            const id = $(this).data('id');
            const btn = $(this);
            btn.prop('disabled', true);

            $.ajax({
                url: `${baseUrl}/${id}/toggle-status`,
                type: 'PATCH',
                success: function (res) {
                    showAlert('success', res.message || '{{ __("Academic subject status updated successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    btn.prop('disabled', false);
                    const msg = xhr.responseJSON?.message || '{{ __("Failed to update status.") }}';
                    showAlert('danger', msg);
                }
            });
        });

        // 6. Delete Academic Subject Modal
        const deleteModalEl = document.getElementById('deleteAcademicSubjectModal');
        const deleteModal = new bootstrap.Modal(deleteModalEl);
        let targetDeleteId = null;

        $(document).on('click', '.btn-delete-academic-subject', function () {
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
                    showAlert('success', res.message || '{{ __("Academic subject deleted successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    confirmBtn.prop('disabled', false).text('{{ __("Yes, Delete") }}');
                    deleteModal.hide();
                    const msg = xhr.responseJSON?.message || '{{ __("Failed to delete academic subject.") }}';
                    showAlert('danger', msg);
                }
            });
        });

        // 7. Clone Academic Subject Modal
        const cloneModalEl = document.getElementById('cloneAcademicSubjectModal');
        const cloneModal = new bootstrap.Modal(cloneModalEl);
        let targetCloneId = null;

        $(document).on('click', '.btn-clone-academic-subject', function () {
            targetCloneId = $(this).data('id');
            const name = $(this).data('name') || '';
            $('#clone_target_name').text(name);
            cloneModal.show();
        });

        $('#confirm_clone_btn').on('click', function () {
            if (!targetCloneId) return;
            const confirmBtn = $(this);
            confirmBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Cloning...") }}');

            $.ajax({
                url: `${baseUrl}/${targetCloneId}/clone`,
                type: 'POST',
                success: function (res) {
                    confirmBtn.prop('disabled', false).text('{{ __("Yes, Clone") }}');
                    cloneModal.hide();
                    showAlert('success', res.message || '{{ __("Academic subject cloned successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    confirmBtn.prop('disabled', false).text('{{ __("Yes, Clone") }}');
                    cloneModal.hide();
                    const msg = xhr.responseJSON?.message || '{{ __("Failed to clone academic subject.") }}';
                    showAlert('danger', msg);
                }
            });
        });
    }

    if (document.readyState === 'complete') {
        initAcademicSubjects();
    } else {
        window.addEventListener('load', initAcademicSubjects);
    }
})();
</script>
@endsection
