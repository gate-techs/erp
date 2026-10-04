<!-- Name in Arabic -->
<div class="mb-3">
    <label class="form-label required fw-semibold" for="{{ $prefix }}_name_ar">
        {{ __('Name in Arabic') }} <span class="text-danger">*</span>
    </label>
    <input type="text" class="form-control" id="{{ $prefix }}_name_ar" name="name_ar"
           placeholder="{{ __('e.g. تطوير المنصة الإلكترونية') }}" dir="rtl" required>
</div>

<!-- Name in English -->
<div class="mb-3">
    <label class="form-label required fw-semibold" for="{{ $prefix }}_name_en">
        {{ __('Name in English') }} <span class="text-danger">*</span>
    </label>
    <input type="text" class="form-control" id="{{ $prefix }}_name_en" name="name_en"
           placeholder="{{ __('e.g. Platform Development') }}" dir="ltr" required>
</div>

<!-- Department Selection (Select2) -->
<div class="mb-3">
    <label class="form-label fw-semibold" for="{{ $prefix }}_department_id">
        {{ __('Department') }} <small class="text-muted">({{ __('Optional') }})</small>
    </label>
    <select class="form-select select2" id="{{ $prefix }}_department_id" name="department_id"
            data-placeholder="{{ __('Select Department') }}" data-allow-clear="true">
        <option value="">{{ __('Select Department') }}</option>
        @foreach ($departments as $dept)
            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
        @endforeach
    </select>
</div>

<!-- Date Range (Start & End Date with Flatpickr) -->
<div class="row g-2 mb-3">
    <div class="col-6">
        <label class="form-label fw-semibold" for="{{ $prefix }}_start_date">{{ __('Start Date') }}</label>
        <input type="text" class="form-control js-flatpickr-date" id="{{ $prefix }}_start_date" name="start_date" placeholder="YYYY-MM-DD" autocomplete="off">
    </div>
    <div class="col-6">
        <label class="form-label fw-semibold" for="{{ $prefix }}_end_date">{{ __('End Date') }}</label>
        <input type="text" class="form-control js-flatpickr-date" id="{{ $prefix }}_end_date" name="end_date" placeholder="YYYY-MM-DD" autocomplete="off">
    </div>
</div>

<!-- Description in Arabic -->
<div class="mb-3">
    <label class="form-label fw-semibold" for="{{ $prefix }}_description_ar">
        {{ __('Description in Arabic') }} <small class="text-muted">({{ __('Optional') }})</small>
    </label>
    <textarea class="form-control" id="{{ $prefix }}_description_ar" name="description_ar" rows="3"
              placeholder="{{ __('تفاصيل وأهداف المجموعة...') }}" dir="rtl"></textarea>
</div>

<!-- Description in English -->
<div class="mb-3">
    <label class="form-label fw-semibold" for="{{ $prefix }}_description_en">
        {{ __('Description in English') }} <small class="text-muted">({{ __('Optional') }})</small>
    </label>
    <textarea class="form-control" id="{{ $prefix }}_description_en" name="description_en" rows="3"
              placeholder="{{ __('Group objectives and details...') }}" dir="ltr"></textarea>
</div>

<!-- Status (Is Active) -->
<div class="mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="{{ $prefix }}_is_active" name="is_active" value="1" checked>
        <label class="form-check-label fw-semibold" for="{{ $prefix }}_is_active">{{ __('Active Status') }}</label>
    </div>
</div>
