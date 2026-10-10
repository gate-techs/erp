@extends('layouts/contentNavbarLayout')

@section('title', __('Guardians') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Academic Management') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Guardians') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-user-check text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Guardians') }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddGuardian" aria-controls="offcanvasAddGuardian">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add Guardian') }}</span>
        </button>
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

            <!-- 3. Status Filter & Reset Button -->
            <div class="col-12 col-md-4">
                <div class="row g-2">
                    <div class="col-8">
                        <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                        <select id="filter_status" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label d-none d-md-block">&nbsp;</label>
                        <button type="button" id="btn_reset_filters" class="btn btn-label-secondary btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                            <i class="icon-base bx bx-reset font-18"></i>
                            <span>{{ __('Reset') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Guardians DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="guardiansTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Guardian Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Phone Number') }}</th>
                    <th>{{ __('Institution') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th class="text-center" style="width: 90px;">{{ __('Students') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 130px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add Guardian                                                -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddGuardian" aria-labelledby="offcanvasAddGuardianLabel" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3">
        <h5 id="offcanvasAddGuardianLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-user-plus text-primary font-22"></i>
            <span>{{ __('Add Guardian') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form id="formAddGuardian" enctype="multipart/form-data">
            @csrf

            <!-- Photo Upload with Preview (Row 1) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small">{{ __('Photo') }}</label>
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-xl border rounded p-1" style="width: 58px; height: 58px; flex-shrink: 0;">
                        <img id="add_photo_preview" src="{{ asset('assets/img/avatars/1.png') }}" alt="avatar" class="rounded w-100 h-100 object-fit-cover">
                    </div>
                    <div class="flex-grow-1">
                        <input type="file" name="photo" id="add_photo" class="form-control" accept="image/*">
                        <small class="text-muted d-block mt-1">{{ __('Allowed formats: JPG, PNG, WEBP. Max size: 2MB') }}</small>
                    </div>
                </div>
                <div class="invalid-feedback d-block" id="formAddGuardian_photo_error"></div>
            </div>

            <!-- Staff Link Switch (Row 2) -->
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="add_is_employee" name="is_employee" value="1">
                <label class="form-check-label fw-semibold text-heading small" for="add_is_employee">
                    {{ __('Is Staff or Teacher?') }}
                </label>
            </div>

            <!-- Staff Selection Dropdown (Hidden by default) -->
            <div id="add_staff_select_wrapper" class="mb-3 d-none">
                <label class="form-label fw-semibold text-heading small required">{{ __('Select Staff / Teacher...') }}</label>
                <select name="existing_user_id" id="add_existing_user_id" class="select2 form-select" data-placeholder="{{ __('Select Staff / Teacher...') }}">
                    <option value="">{{ __('Select Staff / Teacher...') }}</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->user_id }}"
                            data-name="{{ $emp->translate(app()->getLocale())?->name ?? $emp->translate('ar')?->name ?? '' }}"
                            data-civil-id="{{ $emp->national_id ?? '' }}"
                            data-phone="{{ $emp->phone }}"
                            data-email="{{ $emp->email }}"
                            data-photo="{{ $emp->avatar_url }}">
                            {{ $emp->translate(app()->getLocale())?->name ?? $emp->translate('ar')?->name ?? '' }} ({{ $emp->employee_code }})
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formAddGuardian_existing_user_id_error"></div>
            </div>

            <!-- Guardian Name (Arabic) (Row 3) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading small">{{ __('Guardian Name (Arabic)') }}</label>
                <input type="text" name="name_ar" id="add_name_ar" class="form-control" placeholder="{{ __('e.g. محمد أحمد') }}" required>
                <div class="invalid-feedback d-block" id="formAddGuardian_name_ar_error"></div>
            </div>

            <!-- Guardian Name (English) (Row 4) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small">{{ __('Guardian Name (English)') }}</label>
                <input type="text" name="name_en" id="add_name_en" class="form-control" placeholder="{{ __('e.g. Mohamed Ahmed') }}">
                <div class="invalid-feedback d-block" id="formAddGuardian_name_en_error"></div>
            </div>

            <!-- Civil ID (Row 5) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small">{{ __('Civil ID') }}</label>
                <input type="text" name="civil_id" id="add_civil_id" class="form-control" placeholder="{{ __('Civil ID') }}">
                <div class="invalid-feedback d-block" id="formAddGuardian_civil_id_error"></div>
            </div>

            <!-- Educational Institution / Nursery (Row 5) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading small">{{ __('Educational Institution / Nursery') }}</label>
                <select name="institution_id" id="add_institution_id" class="select2 form-select" required>
                    <option value="">{{ __('Select Institution...') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formAddGuardian_institution_id_error"></div>
            </div>

            <!-- Branch (Row 6) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small">{{ __('Branch') }}</label>
                <select name="branch_id" id="add_branch_id" class="select2 form-select">
                    <option value="">{{ __('Select Branch...') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formAddGuardian_branch_id_error"></div>
            </div>

            <!-- Phone Number (Row 7) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading small">{{ __('Phone Number') }}</label>
                <input type="tel" name="phone" id="add_phone" class="form-control js-phone-input iti__tel-input" dir="ltr" required>
                <div class="invalid-feedback d-block" id="formAddGuardian_phone_error"></div>
            </div>

            <!-- Email (Row 8) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small">{{ __('Email') }}</label>
                <input type="email" name="email" id="add_email" class="form-control" placeholder="parent@example.com" dir="ltr">
                <div class="invalid-feedback d-block" id="formAddGuardian_email_error"></div>
            </div>

            <!-- Password (Row 9) -->
            <div class="mb-3" id="add_password_wrapper">
                <label class="form-label fw-semibold text-heading small required">{{ __('Password') }}</label>
                <div class="input-group">
                    <input type="password" name="password" id="add_password" class="form-control" placeholder="******" minlength="6">
                    <button class="btn btn-outline-secondary toggle-password-visibility" type="button">
                        <i class="bx bx-show"></i>
                    </button>
                </div>
                <div class="invalid-feedback d-block" id="formAddGuardian_password_error"></div>
            </div>

            <!-- Status (Row 10) -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading small required">{{ __('Status') }}</label>
                <select name="status" id="add_status" class="form-select" required>
                    <option value="active" selected>{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
                <div class="invalid-feedback d-block" id="formAddGuardian_status_error"></div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" id="btn_submit_add" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-check font-18"></i>
                    <span>{{ __('Save Guardian') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Guardian                                               -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditGuardian" aria-labelledby="offcanvasEditGuardianLabel" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3">
        <h5 id="offcanvasEditGuardianLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-primary font-22"></i>
            <span>{{ __('Edit Guardian') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form id="formEditGuardian" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="id" id="edit_guardian_id">

            <!-- Photo Upload with Preview (Row 1) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small">{{ __('Photo') }}</label>
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-xl border rounded p-1" style="width: 58px; height: 58px; flex-shrink: 0;">
                        <img id="edit_photo_preview" src="{{ asset('assets/img/avatars/1.png') }}" alt="avatar" class="rounded w-100 h-100 object-fit-cover">
                    </div>
                    <div class="flex-grow-1">
                        <input type="file" name="photo" id="edit_photo" class="form-control" accept="image/*">
                        <small class="text-muted d-block mt-1">{{ __('Allowed formats: JPG, PNG, WEBP. Max size: 2MB') }}</small>
                    </div>
                </div>
                <div class="invalid-feedback d-block" id="formEditGuardian_photo_error"></div>
            </div>

            <!-- Staff Status Badge (If linked) (Row 2) -->
            <div id="edit_staff_badge_wrapper" class="mb-3 d-none">
                <span class="badge bg-label-warning py-2 px-3 d-inline-flex align-items-center gap-1 font-13">
                    <i class="bx bx-briefcase font-16"></i>
                    <span>{{ __('Staff & Guardian') }}</span>
                </span>
            </div>

            <!-- Guardian Name (Arabic) (Row 3) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading small">{{ __('Guardian Name (Arabic)') }}</label>
                <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required>
                <div class="invalid-feedback d-block" id="formEditGuardian_name_ar_error"></div>
            </div>

            <!-- Guardian Name (English) (Row 4) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small">{{ __('Guardian Name (English)') }}</label>
                <input type="text" name="name_en" id="edit_name_en" class="form-control">
                <div class="invalid-feedback d-block" id="formEditGuardian_name_en_error"></div>
            </div>

            <!-- Civil ID (Row 5) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small">{{ __('Civil ID') }}</label>
                <input type="text" name="civil_id" id="edit_civil_id" class="form-control" placeholder="{{ __('Civil ID') }}">
                <div class="invalid-feedback d-block" id="formEditGuardian_civil_id_error"></div>
            </div>

            <!-- Educational Institution / Nursery (Row 5) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading small">{{ __('Educational Institution / Nursery') }}</label>
                <select name="institution_id" id="edit_institution_id" class="select2 form-select" required>
                    <option value="">{{ __('Select Institution...') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formEditGuardian_institution_id_error"></div>
            </div>

            <!-- Branch (Row 6) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small">{{ __('Branch') }}</label>
                <select name="branch_id" id="edit_branch_id" class="select2 form-select">
                    <option value="">{{ __('Select Branch...') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formEditGuardian_branch_id_error"></div>
            </div>

            <!-- Phone Number (Row 7) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading small">{{ __('Phone Number') }}</label>
                <input type="tel" name="phone" id="edit_phone" class="form-control js-phone-input iti__tel-input" dir="ltr" required>
                <div class="invalid-feedback d-block" id="formEditGuardian_phone_error"></div>
            </div>

            <!-- Email (Row 8) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small">{{ __('Email') }}</label>
                <input type="email" name="email" id="edit_email" class="form-control" dir="ltr">
                <div class="invalid-feedback d-block" id="formEditGuardian_email_error"></div>
            </div>

            <!-- Optional Password (Row 9) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small">{{ __('Password (leave blank to keep unchanged)') }}</label>
                <div class="input-group">
                    <input type="password" name="password" id="edit_password" class="form-control" placeholder="******" minlength="6">
                    <button class="btn btn-outline-secondary toggle-password-visibility" type="button">
                        <i class="bx bx-show"></i>
                    </button>
                </div>
                <div class="invalid-feedback d-block" id="formEditGuardian_password_error"></div>
            </div>

            <!-- Status (Row 10) -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading small required">{{ __('Status') }}</label>
                <select name="status" id="edit_status" class="form-select" required>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
                <div class="invalid-feedback d-block" id="formEditGuardian_status_error"></div>
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
<!-- 3. Modal: View Guardian Details                                           -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalViewGuardian" tabindex="-1" aria-labelledby="modalViewGuardianLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-heading d-flex align-items-center gap-2 mb-0" id="modalViewGuardianLabel">
                    <i class="icon-base bx bx-user-check text-primary font-22"></i>
                    <span>{{ __('Guardian Details') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="avatar avatar-xl border rounded-circle p-1" style="width: 72px; height: 72px;">
                        <img id="view_photo" src="{{ asset('assets/img/avatars/1.png') }}" class="rounded-circle w-100 h-100 object-fit-cover" alt="avatar">
                    </div>
                    <div>
                        <h5 class="fw-bold text-heading mb-1" id="view_name_ar">-</h5>
                        <h6 class="text-muted small mb-1" id="view_name_en">-</h6>
                        <span id="view_code_badge" class="badge bg-label-primary font-12">-</span>
                        <span id="view_staff_badge" class="badge bg-label-warning font-12 d-none">{{ __('Staff & Guardian') }}</span>
                    </div>
                </div>

                <div class="p-3 bg-light rounded">
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block">{{ __('Phone Number') }}:</small>
                            <span id="view_phone" class="fw-semibold text-heading small" dir="ltr">-</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">{{ __('Email') }}:</small>
                            <span id="view_email" class="fw-semibold text-heading small">-</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">{{ __('Institution') }}:</small>
                            <span id="view_inst_name" class="fw-semibold text-heading small">-</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">{{ __('Branch') }}:</small>
                            <span id="view_branch_name" class="fw-semibold text-heading small">-</span>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">{{ __('Status') }}:</small>
                            <span id="view_status_badge" class="badge bg-label-success">-</span>
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
<!-- 4. Modal: Delete Guardian Confirmation                                    -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalDeleteGuardian" tabindex="-1" aria-labelledby="modalDeleteGuardianLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="icon-base bx bx-trash font-28 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Delete Guardian') }}</h5>
                <p class="text-muted small mb-4">{{ __('Are you sure you want to delete') }} <strong id="delete_guardian_name" class="text-heading"></strong>? {{ __('This action cannot be undone.') }}</p>
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
(function () {
    //------------------------------------------
    function initGuardians() {
        const $ = window.jQuery || window.$;
        if (!$ || !$.fn.DataTable) {
            setTimeout(initGuardians, 50);
            return;
        }

        const baseUrl = "{{ url('academic/guardians') }}";
        const dataUrl = "{{ route('academic.guardians.data') }}";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        //------------------------------------------
        function showToast(type, message, title = '') {
            const mappedType = type === 'danger' ? 'error' : type;
            if (window.toastr && typeof window.toastr[mappedType] === 'function') {
                window.toastr[mappedType](message, title);
            } else if (window.notify && typeof window.notify[mappedType] === 'function') {
                window.notify[mappedType](message, title);
            }
        }

        //------------------------------------------
        function clearFormErrors(formId) {
            $(`#${formId} .is-invalid`).removeClass('is-invalid');
            $(`#${formId} .invalid-feedback`).text('');
        }

        //------------------------------------------
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

        //------------------------------------------
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

        applySelect2();

        $('#offcanvasAddGuardian, #offcanvasEditGuardian').on('shown.bs.offcanvas', function () {
            applySelect2(this);
        });

        //------------------------------------------
        // Dynamic Branch Loading Helper
        function loadBranchesForInstitution(institutionId, targetSelectId, selectedBranchId = null) {
            const branchSelect = $(targetSelectId);
            branchSelect.html('<option value="">{{ __("Loading...") }}</option>').trigger('change');

            if (!institutionId) {
                branchSelect.html('<option value="">{{ __("Select Branch...") }}</option>').trigger('change');
                return;
            }

            $.ajax({
                url: `${baseUrl}/branches/${institutionId}`,
                type: 'GET',
                success: function (res) {
                    let options = '<option value="">{{ __("Select Branch...") }}</option>';
                    if (res.success && res.data) {
                        res.data.forEach(function (branch) {
                            const isSelected = selectedBranchId && String(selectedBranchId) === String(branch.id) ? 'selected' : '';
                            options += `<option value="${branch.id}" ${isSelected}>${branch.name}</option>`;
                        });
                    }
                    branchSelect.html(options).trigger('change');
                },
                error: function () {
                    branchSelect.html('<option value="">{{ __("Select Branch...") }}</option>').trigger('change');
                }
            });
        }

        $('#add_institution_id').on('change', function () {
            loadBranchesForInstitution($(this).val(), '#add_branch_id');
        });

        $('#edit_institution_id').on('change', function () {
            loadBranchesForInstitution($(this).val(), '#edit_branch_id');
        });

        //------------------------------------------
        // Staff Switch Handler in Add Form
        $('#add_is_employee').on('change', function () {
            const isChecked = $(this).is(':checked');
            const wrapper = $('#add_staff_select_wrapper');
            const passwordWrapper = $('#add_password_wrapper');

            if (isChecked) {
                wrapper.removeClass('d-none');
                passwordWrapper.addClass('d-none');
                $('#add_password').val('');
                applySelect2(wrapper);
            } else {
                wrapper.addClass('d-none');
                passwordWrapper.removeClass('d-none');
                $('#add_existing_user_id').val('').trigger('change');
            }
        });

        $('#add_existing_user_id').on('change', function () {
            const selected = $(this).find('option:selected');
            if (selected.val()) {
                const name = selected.data('name') || '';
                const civilId = selected.data('civil-id') || '';
                const phone = selected.data('phone') || '';
                const email = selected.data('email') || '';
                const photo = selected.data('photo') || '';

                if (name) $('#add_name_ar').val(name);
                if (civilId) $('#add_civil_id').val(civilId);
                if (phone) {
                    const phoneInput = document.getElementById('add_phone');
                    phoneInput.value = phone;
                    if (window.intlTelInputGlobals) {
                        const iti = window.intlTelInputGlobals.getInstance(phoneInput);
                        if (iti) iti.setNumber(phone);
                    }
                    phoneInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
                if (email) $('#add_email').val(email);
                if (photo) $('#add_photo_preview').attr('src', photo);
            }
        });

        //------------------------------------------
        // Image preview handlers
        $('#add_photo').on('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => $('#add_photo_preview').attr('src', e.target.result);
                reader.readAsDataURL(file);
            }
        });

        $('#edit_photo').on('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => $('#edit_photo_preview').attr('src', e.target.result);
                reader.readAsDataURL(file);
            }
        });

        //------------------------------------------
        // Toggle password visibility
        $('.toggle-password-visibility').on('click', function () {
            const input = $(this).siblings('input');
            const icon = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('bx-show').addClass('bx-hide');
            } else {
                input.attr('type', 'password');
                icon.removeClass('bx-hide').addClass('bx-show');
            }
        });

        //------------------------------------------
        // DataTable Initialization
        const table = $('#guardiansTable').DataTable({
            processing: true,
            serverSide: true,
            pagingType: 'simple_numbers',
            language: {
                emptyTable: `
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="icon-base bx bx-user-x font-32 text-secondary"></i>
                        </div>
                        <h6 class="fw-bold mb-1">{{ __('No guardians found') }}</h6>
                        <p class="text-muted small mb-0">{{ __('Click "Add Guardian" to create the first guardian.') }}</p>
                    </div>
                `
            },
            ajax: {
                url: dataUrl,
                data: function (d) {
                    d.institution_id = $('#filter_institution_id').val();
                    d.branch_id = $('#filter_branch_id').val();
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
                { data: 'email', name: 'email', defaultContent: '-' },
                { data: 'phone_formatted', name: 'phone', className: 'text-center' },
                { data: 'institution_name', name: 'institution_id', defaultContent: '-' },
                { data: 'branch_name', name: 'branch_id', defaultContent: '-' },
                { data: 'students_count', name: 'students_count', orderable: false, searchable: false, className: 'text-center' },
                { data: 'status_badge', name: 'status', className: 'text-center' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[0, 'desc']]
        });

        $('#filter_institution_id, #filter_branch_id, #filter_status').on('change', function () {
            table.ajax.reload();
        });

        $('#btn_reset_filters').on('click', function () {
            $('#filter_institution_id').val('').trigger('change');
            $('#filter_branch_id').val('').trigger('change');
            $('#filter_status').val('').trigger('change');
            table.ajax.reload();
        });

        //------------------------------------------
        // Submit Add Guardian Form
        $('#formAddGuardian').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = $('#btn_submit_add');
            const originalBtnHtml = submitBtn.html();

            clearFormErrors('formAddGuardian');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Saving...") }}');

            const formData = new FormData(this);

            $.ajax({
                url: baseUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    const offcanvasEl = document.getElementById('offcanvasAddGuardian');
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                    if (bsOffcanvas) bsOffcanvas.hide();

                    form[0].reset();
                    $('#add_civil_id').val('');
                    $('#add_is_employee').prop('checked', false).trigger('change');
                    form.find('select.select2').val('').trigger('change');
                    $('#add_status').val('active').trigger('change');
                    $('#add_photo_preview').attr('src', '{{ asset("assets/img/avatars/1.png") }}');

                    const phoneInput = document.getElementById('add_phone');
                    phoneInput.value = '';
                    phoneInput.dispatchEvent(new Event('input', { bubbles: true }));

                    showToast('success', res.message || '{{ __("Guardian created successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        showFormErrors('formAddGuardian', xhr.responseJSON.errors);
                    } else {
                        const msg = xhr.responseJSON?.message || '{{ __("Error saving guardian.") }}';
                        showToast('error', msg);
                    }
                }
            });
        });

        //------------------------------------------
        // Edit Guardian - Fetch & Populate
        const editOffcanvasEl = document.getElementById('offcanvasEditGuardian');
        const editOffcanvas = new bootstrap.Offcanvas(editOffcanvasEl);

        $(document).on('click', '.edit-record-btn', function () {
            const id = $(this).data('id');
            clearFormErrors('formEditGuardian');

            $.ajax({
                url: `${baseUrl}/${id}/edit`,
                type: 'GET',
                success: function (res) {
                    if (res.success && res.data) {
                        const g = res.data;
                        $('#edit_guardian_id').val(g.id);
                        $('#edit_name_ar').val(g.name_ar);
                        $('#edit_name_en').val(g.name_en);
                        $('#edit_civil_id').val(g.civil_id || '');
                        $('#edit_institution_id').val(g.institution_id).trigger('change');
                        $('#edit_email').val(g.email);
                        $('#edit_password').val('');
                        $('#edit_status').val(g.status);
                        $('#edit_photo_preview').attr('src', g.photo_url || '{{ asset("assets/img/avatars/1.png") }}');

                        const phoneInput = document.getElementById('edit_phone');
                        phoneInput.value = g.phone || '';
                        if (window.intlTelInputGlobals) {
                            const iti = window.intlTelInputGlobals.getInstance(phoneInput);
                            if (iti) iti.setNumber(g.phone || '');
                        }
                        phoneInput.dispatchEvent(new Event('input', { bubbles: true }));

                        if (g.is_employee) {
                            $('#edit_staff_badge_wrapper').removeClass('d-none');
                        } else {
                            $('#edit_staff_badge_wrapper').addClass('d-none');
                        }

                        // Set branch after loading options
                        loadBranchesForInstitution(g.institution_id, '#edit_branch_id', g.branch_id);

                        editOffcanvas.show();
                    }
                },
                error: function () {
                    showToast('error', '{{ __("Error loading guardian details.") }}');
                }
            });
        });

        //------------------------------------------
        // Submit Edit Guardian Form
        $('#formEditGuardian').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const id = $('#edit_guardian_id').val();
            const submitBtn = $('#btn_submit_edit');
            const originalBtnHtml = submitBtn.html();

            clearFormErrors('formEditGuardian');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Updating...") }}');

            const formData = new FormData(this);

            $.ajax({
                url: `${baseUrl}/${id}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    editOffcanvas.hide();
                    showToast('success', res.message || '{{ __("Guardian updated successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        showFormErrors('formEditGuardian', xhr.responseJSON.errors);
                    } else {
                        const msg = xhr.responseJSON?.message || '{{ __("Error updating guardian.") }}';
                        showToast('error', msg);
                    }
                }
            });
        });

        //------------------------------------------
        // View Guardian Details Modal
        const viewModal = new bootstrap.Modal(document.getElementById('modalViewGuardian'));

        $(document).on('click', '.view-record-btn', function () {
            const id = $(this).data('id');

            $.ajax({
                url: `${baseUrl}/${id}`,
                type: 'GET',
                success: function (res) {
                    if (res.success && res.data) {
                        const g = res.data;
                        const nameAr = g.translations?.find(t => t.locale === 'ar')?.name || g.name || '-';
                        const nameEn = g.translations?.find(t => t.locale === 'en')?.name || '-';

                        $('#view_name_ar').text(nameAr);
                        $('#view_name_en').text(nameEn);
                        $('#view_code_badge').text(g.guardian_code || `GRD-${g.id}`);
                        $('#view_phone').text(g.phone || '-');
                        $('#view_email').text(g.email || g.user?.email || '-');
                        $('#view_photo').attr('src', g.photo_url || '{{ asset("assets/img/avatars/1.png") }}');
                        $('#view_inst_name').text(g.institution?.name || '-');
                        $('#view_branch_name').text(g.branch?.name || '{{ __("All Branches") }}');

                        const isActive = g.status === 'active';
                        $('#view_status_badge').text(isActive ? '{{ __("Active") }}' : '{{ __("Inactive") }}')
                            .attr('class', isActive ? 'badge bg-label-success' : 'badge bg-label-danger');

                        if (g.user && g.user.employee) {
                            $('#view_staff_badge').removeClass('d-none');
                        } else {
                            $('#view_staff_badge').addClass('d-none');
                        }

                        viewModal.show();
                    }
                }
            });
        });

        //------------------------------------------
        // Toggle Status Handler
        $(document).on('click', '.toggle-status-btn', function () {
            const id = $(this).data('id');
            const badge = $(this);

            $.ajax({
                url: `${baseUrl}/${id}/toggle-status`,
                type: 'PATCH',
                success: function (res) {
                    if (res.success) {
                        const isActive = res.status === 'active';
                        badge.text(isActive ? '{{ __("Active") }}' : '{{ __("Inactive") }}');
                        badge.attr('class', `badge ${isActive ? 'bg-label-success' : 'bg-label-danger'} cursor-pointer toggle-status-btn`);
                        showToast('success', res.message || '{{ __("Status updated successfully.") }}');
                    }
                },
                error: function () {
                    showToast('error', '{{ __("Error toggling status.") }}');
                }
            });
        });

        //------------------------------------------
        // Delete Guardian Handler
        let deleteGuardianId = null;
        const deleteModal = new bootstrap.Modal(document.getElementById('modalDeleteGuardian'));

        $(document).on('click', '.delete-record-btn', function () {
            deleteGuardianId = $(this).data('id');
            const name = $(this).data('name') || $(this).closest('tr').find('.guardian-name-text').text() || '{{ __("this guardian") }}';
            $('#delete_guardian_name').text(name);
            deleteModal.show();
        });

        $('#btn_confirm_delete').on('click', function () {
            if (!deleteGuardianId) return;

            const btn = $(this);
            const originalText = btn.text();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>');

            $.ajax({
                url: `${baseUrl}/${deleteGuardianId}`,
                type: 'DELETE',
                success: function (res) {
                    btn.prop('disabled', false).text(originalText);
                    deleteModal.hide();
                    deleteGuardianId = null;
                    showToast('success', res.message || '{{ __("Guardian deleted successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    btn.prop('disabled', false).text(originalText);
                    const msg = xhr.responseJSON?.message || '{{ __("Error deleting guardian.") }}';
                    showToast('error', msg);
                }
            });
        });
    }

    // Initialize on ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGuardians);
    } else {
        initGuardians();
    }
})();
</script>
@endsection
