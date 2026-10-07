@extends('layouts/contentNavbarLayout')

@section('title', __('Academic Weekly Plans') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Academic Management') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Academic Weekly Plans') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-calendar-week text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Academic Weekly Plans') }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddAcademicWeeklyPlan" aria-controls="offcanvasAddAcademicWeeklyPlan">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add Academic Weekly Plan') }}</span>
        </button>
    </div>
</div>

<!-- Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Institution Filter -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Educational Institution / Nursery') }}</label>
                <select id="filter_institution_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Institutions') }}" data-allow-clear="true">
                    <option value="">{{ __('All Institutions') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Branch Filter -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select id="filter_branch_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Branches') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Academic Stage Filter -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Stage') }}</label>
                <select id="filter_academic_stage_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Academic Stages') }}" data-allow-clear="true">
                    <option value="">{{ __('All Academic Stages') }}</option>
                    @foreach ($academicStages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->name ?? $stage->code }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Academic Class Filter -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Class') }}</label>
                <select id="filter_academic_class_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Academic Classes') }}" data-allow-clear="true">
                    <option value="">{{ __('All Academic Classes') }}</option>
                    @foreach ($academicClasses as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->translate(app()->getLocale())?->name ?? $cls->translate('ar')?->name ?? $cls->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 5. Status Filter -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}">{{ __($status === 'active' ? 'Active' : 'Inactive') }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 6. Reset Button -->
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
        <table id="academicWeeklyPlansTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Plan Title') }}</th>
                    <th>{{ __('Institution') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th>{{ __('Academic Stage') }}</th>
                    <th>{{ __('Academic Class') }}</th>
                    <th>{{ __('Dates') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add Academic Weekly Plan                                     -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddAcademicWeeklyPlan" aria-labelledby="offcanvasAddAcademicWeeklyPlanLabel" style="width: 580px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddAcademicWeeklyPlanLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-plus-circle text-primary font-22"></i>
            <span>{{ __('Add Academic Weekly Plan') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="addAcademicWeeklyPlanForm" action="{{ route('academic.weekly-plans.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- 1. Institution (Full Row) -->
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

            <!-- 2. Branch (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Branch') }}</label>
                <select name="branch_id" id="add_branch_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('Select Branch...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Branch...') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_branch_id_error"></div>
            </div>

            <!-- 3. Academic Stage (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Stage') }} <span class="text-danger">*</span></label>
                <select name="academic_stage_id" id="add_academic_stage_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Stage...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Stage...') }}</option>
                    @foreach ($academicStages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->name ?? $stage->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_academic_stage_id_error"></div>
            </div>

            <!-- 4. Academic Class (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Class') }} <span class="text-danger">*</span></label>
                <select name="academic_class_id" id="add_academic_class_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Class...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Class...') }}</option>
                    @foreach ($academicClasses as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->translate(app()->getLocale())?->name ?? $cls->translate('ar')?->name ?? $cls->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_academic_class_id_error"></div>
            </div>

            <!-- 5. Plan Title Arabic (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Plan Title (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="title_ar" id="add_title_ar" class="form-control" placeholder="{{ __('Plan Title (Arabic)') }}" required maxlength="255">
                <div class="invalid-feedback d-block" id="add_title_ar_error"></div>
            </div>

            <!-- 6. Plan Title English (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Plan Title (English)') }} <span class="text-danger">*</span></label>
                <input type="text" name="title_en" id="add_title_en" class="form-control" placeholder="{{ __('Plan Title (English)') }}" required maxlength="255" dir="ltr">
                <div class="invalid-feedback d-block" id="add_title_en_error"></div>
            </div>

            <!-- 7. Start Date (Full Row with Flatpickr) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Start Date') }}</label>
                <input type="text" name="start_date" id="add_start_date" class="form-control js-flatpickr-date flatpickr-input" placeholder="YYYY-MM-DD" autocomplete="off">
                <div class="invalid-feedback d-block" id="add_start_date_error"></div>
            </div>

            <!-- 8. End Date (Full Row with Flatpickr) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('End Date') }}</label>
                <input type="text" name="end_date" id="add_end_date" class="form-control js-flatpickr-date flatpickr-input" placeholder="YYYY-MM-DD" autocomplete="off">
                <div class="invalid-feedback d-block" id="add_end_date_error"></div>
            </div>

            <!-- 9. Status (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Status') }}</label>
                <select name="status" id="add_status" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('Select Status') }}">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" {{ $status === 'active' ? 'selected' : '' }}>{{ __($status === 'active' ? 'Active' : 'Inactive') }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_status_error"></div>
            </div>

            <!-- 10. Details Arabic (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Plan Details / Content (Arabic)') }}</label>
                <textarea name="content_ar" id="add_content_ar" class="form-control" rows="3" placeholder="{{ __('Plan Details / Content (Arabic)') }}"></textarea>
                <div class="invalid-feedback d-block" id="add_content_ar_error"></div>
            </div>

            <!-- 11. Details English (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Plan Details / Content (English)') }}</label>
                <textarea name="content_en" id="add_content_en" class="form-control" rows="3" placeholder="{{ __('Plan Details / Content (English)') }}" dir="ltr"></textarea>
                <div class="invalid-feedback d-block" id="add_content_en_error"></div>
            </div>

            <!-- 12. Attachments (Full Row) -->
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
                    <span>{{ __('Save Academic Weekly Plan') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Academic Weekly Plan                                    -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditAcademicWeeklyPlan" aria-labelledby="offcanvasEditAcademicWeeklyPlanLabel" style="width: 580px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditAcademicWeeklyPlanLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-primary font-22"></i>
            <span>{{ __('Edit Academic Weekly Plan') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="editAcademicWeeklyPlanForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_plan_id" name="id">

            <!-- 1. Institution (Full Row) -->
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

            <!-- 2. Branch (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Branch') }}</label>
                <select name="branch_id" id="edit_branch_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('Select Branch...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Branch...') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_branch_id_error"></div>
            </div>

            <!-- 3. Academic Stage (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Stage') }} <span class="text-danger">*</span></label>
                <select name="academic_stage_id" id="edit_academic_stage_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Stage...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Stage...') }}</option>
                    @foreach ($academicStages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->name ?? $stage->code }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_academic_stage_id_error"></div>
            </div>

            <!-- 4. Academic Class (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Class') }} <span class="text-danger">*</span></label>
                <select name="academic_class_id" id="edit_academic_class_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Class...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Class...') }}</option>
                    @foreach ($academicClasses as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->translate(app()->getLocale())?->name ?? $cls->translate('ar')?->name ?? $cls->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_academic_class_id_error"></div>
            </div>

            <!-- 5. Plan Title Arabic (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Plan Title (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="title_ar" id="edit_title_ar" class="form-control" placeholder="{{ __('Plan Title (Arabic)') }}" required maxlength="255">
                <div class="invalid-feedback d-block" id="edit_title_ar_error"></div>
            </div>

            <!-- 6. Plan Title English (Full Row) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Plan Title (English)') }} <span class="text-danger">*</span></label>
                <input type="text" name="title_en" id="edit_title_en" class="form-control" placeholder="{{ __('Plan Title (English)') }}" required maxlength="255" dir="ltr">
                <div class="invalid-feedback d-block" id="edit_title_en_error"></div>
            </div>

            <!-- 7. Start Date (Full Row with Flatpickr) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Start Date') }}</label>
                <input type="text" name="start_date" id="edit_start_date" class="form-control js-flatpickr-date flatpickr-input" placeholder="YYYY-MM-DD" autocomplete="off">
                <div class="invalid-feedback d-block" id="edit_start_date_error"></div>
            </div>

            <!-- 8. End Date (Full Row with Flatpickr) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('End Date') }}</label>
                <input type="text" name="end_date" id="edit_end_date" class="form-control js-flatpickr-date flatpickr-input" placeholder="YYYY-MM-DD" autocomplete="off">
                <div class="invalid-feedback d-block" id="edit_end_date_error"></div>
            </div>

            <!-- 9. Status (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Status') }}</label>
                <select name="status" id="edit_status" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('Select Status') }}">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}">{{ __($status === 'active' ? 'Active' : 'Inactive') }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_status_error"></div>
            </div>

            <!-- 10. Details Arabic (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Plan Details / Content (Arabic)') }}</label>
                <textarea name="content_ar" id="edit_content_ar" class="form-control" rows="3" placeholder="{{ __('Plan Details / Content (Arabic)') }}"></textarea>
                <div class="invalid-feedback d-block" id="edit_content_ar_error"></div>
            </div>

            <!-- 11. Details English (Full Row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Plan Details / Content (English)') }}</label>
                <textarea name="content_en" id="edit_content_en" class="form-control" rows="3" placeholder="{{ __('Plan Details / Content (English)') }}" dir="ltr"></textarea>
                <div class="invalid-feedback d-block" id="edit_content_en_error"></div>
            </div>

            <!-- 12. Existing Attachments List -->
            <div class="mb-3" id="edit_existing_attachments_container" style="display: none;">
                <label class="form-label fw-semibold text-heading mb-2">{{ __('Current Attachments') }}</label>
                <div class="list-group list-group-flush border rounded-3" id="edit_existing_attachments_list"></div>
            </div>

            <!-- 13. Add More Attachments (Full Row) -->
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
<!-- 3. Modal: View Plan Details                                               -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalViewWeeklyPlan" tabindex="-1" aria-labelledby="modalViewWeeklyPlanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-heading d-flex align-items-center gap-2 mb-0" id="modalViewWeeklyPlanLabel">
                    <i class="icon-base bx bx-calendar-week text-primary font-22"></i>
                    <span id="view_plan_title">{{ __('Plan Details') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-12 col-md-4 text-start">
                        <div class="p-3 bg-light rounded">
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Institution') }}:</small> <span id="view_inst_name" class="fw-semibold text-heading">-</span></div>
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Branch') }}:</small> <span id="view_branch_name" class="fw-semibold text-heading">-</span></div>
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Academic Stage') }}:</small> <span id="view_stage_name" class="badge bg-label-primary">-</span></div>
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Academic Class') }}:</small> <span id="view_class_name" class="badge bg-label-info">-</span></div>
                            <div class="mb-2"><small class="text-muted d-block">{{ __('Start / End Dates') }}:</small> <span id="view_dates" class="fw-semibold text-heading small">-</span></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <h5 class="fw-bold text-primary mb-1" id="view_title_ar">-</h5>
                        <h6 class="text-muted mb-3" id="view_title_en">-</h6>

                        <div class="mb-3">
                            <h6 class="fw-bold text-heading mb-1">{{ __('Plan Details / Content (Arabic)') }}:</h6>
                            <div id="view_content_ar" class="p-3 bg-light rounded text-break" style="white-space: pre-line; min-height: 60px;">-</div>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-bold text-heading mb-1">{{ __('Plan Details / Content (English)') }}:</h6>
                            <div id="view_content_en" class="p-3 bg-light rounded text-break" style="white-space: pre-line; min-height: 60px;" dir="ltr">-</div>
                        </div>

                        <div class="mb-0">
                            <h6 class="fw-bold text-heading mb-2">{{ __('Documents & Attachments') }}:</h6>
                            <div id="view_attachments_list" class="list-group list-group-flush border rounded-3">
                                <span class="text-muted small text-center p-3 d-block">{{ __('No attachments uploaded.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 4. Modal: Confirm Delete                                                  -->
<!-- ========================================================================= -->
<div class="modal fade" id="deleteAcademicWeeklyPlanModal" tabindex="-1" aria-hidden="true">
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
                    {{ __('Are you sure you want to delete the weekly plan') }} <strong id="delete_target_name" class="text-heading"></strong>?
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" id="confirm_delete_btn" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
<script>
(function () {
    function initAcademicWeeklyPlans() {
        const $ = window.jQuery || window.$;
        if (!$ || !$.fn.DataTable) {
            setTimeout(initAcademicWeeklyPlans, 50);
            return;
        }

        const baseUrl = "{{ url('academic/weekly-plans') }}";
        const dataUrl = "{{ route('academic.weekly-plans.data') }}";

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
                    placeholder: $this.data('placeholder') || '',
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
        $('#offcanvasAddAcademicWeeklyPlan, #offcanvasEditAcademicWeeklyPlan').on('shown.bs.offcanvas', function () {
            applySelect2(this);
            applyFlatpickr(this);
        });

        // Initialize Server-Side DataTable matching the theme style
        const table = $('#academicWeeklyPlansTable').DataTable({
            processing: true,
            serverSide: true,
            pagingType: 'simple_numbers',
            language: {
                emptyTable: `
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="icon-base bx bx-calendar-week font-32 text-secondary"></i>
                        </div>
                        <h6 class="fw-bold mb-1">{{ __('No academic weekly plans found') }}</h6>
                        <p class="text-muted small mb-0">{{ __('Click "Add Academic Weekly Plan" to create the first plan.') }}</p>
                    </div>
                `
            },
            ajax: {
                url: dataUrl,
                data: function (d) {
                    d.institution_id = $('#filter_institution_id').val();
                    d.branch_id = $('#filter_branch_id').val();
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
                { data: 'title', name: 'translations.title', className: 'fw-semibold text-heading' },
                { data: 'institution_name', name: 'institution_id', defaultContent: '-' },
                { data: 'branch_name', name: 'branch_id', defaultContent: '-' },
                { data: 'stage_name', name: 'academic_stage_id', defaultContent: '-' },
                { data: 'class_name', name: 'academic_class_id', defaultContent: '-' },
                { data: 'dates_formatted', name: 'start_date', defaultContent: '-' },
                { data: 'status_badge', name: 'status', className: 'text-center' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[0, 'desc']]
        });

        // Filter Change Listeners
        $('#filter_institution_id, #filter_branch_id, #filter_academic_stage_id, #filter_academic_class_id, #filter_status').on('change', function () {
            table.ajax.reload();
        });

        // Reset Filters Listener
        $('#btn_reset_filters').on('click', function () {
            $('#filter_institution_id').val('').trigger('change');
            $('#filter_branch_id').val('').trigger('change');
            $('#filter_academic_stage_id').val('').trigger('change');
            $('#filter_academic_class_id').val('').trigger('change');
            $('#filter_status').val('').trigger('change');
            table.ajax.reload();
        });

        // Dynamic Cascading for Institution -> Branches, Stages, Classes
        function loadCascadingData(instId, branchSelector, stageSelector, classSelector, selectedBranch = '', selectedStage = '', selectedClass = '') {
            if (!instId) {
                $(branchSelector).html('<option value="">{{ __("Select Branch...") }}</option>').trigger('change');
                $(stageSelector).html('<option value="">{{ __("Select Stage...") }}</option>').trigger('change');
                $(classSelector).html('<option value="">{{ __("Select Class...") }}</option>').trigger('change');
                return;
            }

            // 1. Branches
            $.get("{{ url('academic/weekly-plans-ajax/branches') }}/" + instId, function (res) {
                if (res.success) {
                    var options = '<option value="">{{ __("Select Branch...") }}</option>';
                    $.each(res.data, function (i, item) {
                        var isSelected = (String(item.id) === String(selectedBranch)) ? 'selected' : '';
                        options += '<option value="' + item.id + '" ' + isSelected + '>' + item.name + '</option>';
                    });
                    $(branchSelector).html(options).trigger('change');
                }
            });

            // 2. Stages
            $.get("{{ url('academic/weekly-plans-ajax/stages') }}/" + instId, function (res) {
                if (res.success) {
                    var options = '<option value="">{{ __("Select Stage...") }}</option>';
                    $.each(res.data, function (i, item) {
                        var isSelected = (String(item.id) === String(selectedStage)) ? 'selected' : '';
                        options += '<option value="' + item.id + '" ' + isSelected + '>' + item.name + '</option>';
                    });
                    $(stageSelector).html(options).trigger('change');
                }
            });

            // 3. Classes
            loadClassesData(instId, selectedBranch, selectedStage, classSelector, selectedClass);
        }

        function loadClassesData(instId, branchId, stageId, classSelector, selectedClass = '') {
            $.get("{{ route('academic.weekly-plans.ajax.classes') }}", {
                institution_id: instId,
                branch_id: branchId,
                stage_id: stageId
            }, function (res) {
                if (res.success) {
                    var options = '<option value="">{{ __("Select Class...") }}</option>';
                    $.each(res.data, function (i, item) {
                        var isSelected = (String(item.id) === String(selectedClass)) ? 'selected' : '';
                        options += '<option value="' + item.id + '" ' + isSelected + '>' + item.name + '</option>';
                    });
                    $(classSelector).html(options).trigger('change');
                }
            });
        }

        // Change triggers in Add Form
        $('#add_institution_id').on('change', function () {
            loadCascadingData($(this).val(), '#add_branch_id', '#add_academic_stage_id', '#add_academic_class_id');
        });

        $('#add_branch_id, #add_academic_stage_id').on('change', function () {
            loadClassesData($('#add_institution_id').val(), $('#add_branch_id').val(), $('#add_academic_stage_id').val(), '#add_academic_class_id');
        });

        // Change triggers in Edit Form
        $('#edit_institution_id').on('change', function () {
            var curBranch = $('#edit_branch_id').val();
            var curStage = $('#edit_academic_stage_id').val();
            var curClass = $('#edit_academic_class_id').val();
            loadCascadingData($(this).val(), '#edit_branch_id', '#edit_academic_stage_id', '#edit_academic_class_id', curBranch, curStage, curClass);
        });

        $('#edit_branch_id, #edit_academic_stage_id').on('change', function () {
            loadClassesData($('#edit_institution_id').val(), $('#edit_branch_id').val(), $('#edit_academic_stage_id').val(), '#edit_academic_class_id', $('#edit_academic_class_id').val());
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

        // 1. Submit Add Academic Weekly Plan
        $('#addAcademicWeeklyPlanForm').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = $('#btn_submit_add');
            const originalBtnHtml = submitBtn.html();

            clearFormErrors('addAcademicWeeklyPlanForm');
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
                    const offcanvasEl = document.getElementById('offcanvasAddAcademicWeeklyPlan');
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                    if (bsOffcanvas) bsOffcanvas.hide();

                    form[0].reset();
                    $('#add_title_en').data('manually_typed', false);
                    form.find('select.select2, select.select2-selection__rendered').val('').trigger('change');
                    $('#add_status').val('active').trigger('change');
                    $('#add_attachments').val('');

                    showAlert('success', res.message || '{{ __("Academic weekly plan created successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        showFormErrors('addAcademicWeeklyPlanForm', xhr.responseJSON.errors);
                    } else {
                        const msg = xhr.responseJSON?.message || '{{ __("Error saving academic weekly plan.") }}';
                        showAlert('danger', msg);
                    }
                }
            });
        });

        // 2. Open Edit Offcanvas & Fetch Data
        const editOffcanvasEl = document.getElementById('offcanvasEditAcademicWeeklyPlan');
        const editOffcanvas = new bootstrap.Offcanvas(editOffcanvasEl);

        $(document).on('click', '.btn-edit-academic-weekly-plan', function () {
            const id = $(this).data('id');
            clearFormErrors('editAcademicWeeklyPlanForm');
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
                        $('#edit_academic_stage_id').val(item.academic_stage_id ? String(item.academic_stage_id) : '').trigger('change');
                        $('#edit_academic_class_id').val(item.academic_class_id ? String(item.academic_class_id) : '').trigger('change');

                        $('#edit_title_ar').val(item.title_ar || '');
                        $('#edit_title_en').val(item.title_en || '');
                        $('#edit_content_ar').val(item.content_ar || '');
                        $('#edit_content_en').val(item.content_en || '');
                        
                        // Populate flatpickr inputs
                        const startDateInput = document.getElementById('edit_start_date');
                        const endDateInput = document.getElementById('edit_end_date');
                        if (startDateInput._flatpickr) {
                            startDateInput._flatpickr.setDate(item.start_date || '', true);
                        } else {
                            startDateInput.value = item.start_date || '';
                        }
                        if (endDateInput._flatpickr) {
                            endDateInput._flatpickr.setDate(item.end_date || '', true);
                        } else {
                            endDateInput.value = item.end_date || '';
                        }

                        $('#edit_status').val(item.status || 'active').trigger('change');
                        $('#edit_attachments').val('');

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

                        $('#editAcademicWeeklyPlanForm').attr('action', `${baseUrl}/${item.id}`);
                        editOffcanvas.show();
                    }
                },
                error: function () {
                    showAlert('danger', '{{ __("Failed to load academic weekly plan data.") }}');
                }
            });
        });

        // 3. Submit Edit Academic Weekly Plan
        $('#editAcademicWeeklyPlanForm').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = $('#btn_submit_edit');
            const originalBtnHtml = submitBtn.html();
            const actionUrl = form.attr('action');

            clearFormErrors('editAcademicWeeklyPlanForm');
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
                    showAlert('success', res.message || '{{ __("Academic weekly plan updated successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        showFormErrors('editAcademicWeeklyPlanForm', xhr.responseJSON.errors);
                    } else {
                        const msg = xhr.responseJSON?.message || '{{ __("Error updating academic weekly plan.") }}';
                        showAlert('danger', msg);
                    }
                }
            });
        });

        // 4. View Plan Modal
        const viewModalEl = document.getElementById('modalViewWeeklyPlan');
        const viewModal = new bootstrap.Modal(viewModalEl);

        $(document).on('click', '.btn-view-academic-weekly-plan', function () {
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
                        $('#view_content_ar').text(d.content_ar || "{{ __('No description provided.') }}");
                        $('#view_content_en').text(d.content_en || "{{ __('No description provided.') }}");
                        $('#view_inst_name').text(d.institution_name || '-');
                        $('#view_branch_name').text(d.branch_name || '-');
                        $('#view_stage_name').text(d.stage_name || '-');
                        $('#view_class_name').text(d.class_name || '-');
                        $('#view_dates').text((d.start_date || '...') + ' - ' + (d.end_date || '...'));

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
        $(document).on('click', '.btn-toggle-academic-weekly-plan-status', function () {
            const id = $(this).data('id');
            const btn = $(this);
            btn.prop('disabled', true);

            $.ajax({
                url: `${baseUrl}/${id}/toggle-status`,
                type: 'PATCH',
                success: function (res) {
                    showAlert('success', res.message || '{{ __("Academic weekly plan status updated successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    btn.prop('disabled', false);
                    const msg = xhr.responseJSON?.message || '{{ __("Failed to update status.") }}';
                    showAlert('danger', msg);
                }
            });
        });

        // 7. Delete Academic Weekly Plan Modal
        const deleteModalEl = document.getElementById('deleteAcademicWeeklyPlanModal');
        const deleteModal = new bootstrap.Modal(deleteModalEl);
        let targetDeleteId = null;

        $(document).on('click', '.btn-delete-academic-weekly-plan', function () {
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
                    showAlert('success', res.message || '{{ __("Academic weekly plan deleted successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    confirmBtn.prop('disabled', false).text('{{ __("Yes, Delete") }}');
                    deleteModal.hide();
                    const msg = xhr.responseJSON?.message || '{{ __("Failed to delete academic weekly plan.") }}';
                    showAlert('danger', msg);
                }
            });
        });
    }

    if (document.readyState === 'complete') {
        initAcademicWeeklyPlans();
    } else {
        window.addEventListener('load', initAcademicWeeklyPlans);
    }
})();
</script>
@endsection
