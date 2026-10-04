<!-- Task Group (Hidden or Select) -->
@if (!empty($fixedGroupId))
    <input type="hidden" id="{{ $prefix }}_task_group_id" name="task_group_id" value="{{ $fixedGroupId }}">
@else
    <div class="mb-3">
        <label class="form-label fw-semibold" for="{{ $prefix }}_task_group_id">
            {{ __('Task Group') }} <small class="text-muted">({{ __('Optional') }})</small>
        </label>
        <select class="form-select select2" id="{{ $prefix }}_task_group_id" name="task_group_id" data-placeholder="{{ __('Select Group') }}" data-allow-clear="true">
            <option value="">{{ __('No Group') }}</option>
            @if (isset($taskGroups))
                @foreach ($taskGroups as $grp)
                    <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                @endforeach
            @endif
        </select>
    </div>
@endif

<!-- Title in Arabic -->
<div class="mb-3">
    <label class="form-label required fw-semibold" for="{{ $prefix }}_title_ar">
        {{ __('Task Title [Arabic]') }} <span class="text-danger">*</span>
    </label>
    <input type="text" class="form-control" id="{{ $prefix }}_title_ar" name="title_ar"
           placeholder="{{ __('e.g. إعداد التقرير المالي الشهري') }}" dir="rtl" required>
    <div class="invalid-feedback"></div>
</div>

<!-- Title in English -->
<div class="mb-3">
    <label class="form-label fw-semibold" for="{{ $prefix }}_title_en">
        {{ __('Task Title [English]') }} <small class="text-muted">({{ __('Optional') }})</small>
    </label>
    <input type="text" class="form-control" id="{{ $prefix }}_title_en" name="title_en"
           placeholder="{{ __('e.g. Prepare Monthly Financial Report') }}" dir="ltr">
    <div class="invalid-feedback"></div>
</div>

<!-- Employee Assignment -->
<div class="mb-3">
    <label class="form-label fw-semibold" for="{{ $prefix }}_employee_id">
        {{ __('Assign to Employee') }} <small class="text-muted">({{ __('Optional') }})</small>
    </label>
    <select class="form-select select2" id="{{ $prefix }}_employee_id" name="employee_id"
            data-placeholder="{{ __('Select Employee') }}" data-allow-clear="true">
        <option value="">{{ __('Unassigned') }}</option>
        @if (isset($employees))
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
            @endforeach
        @endif
    </select>
    <div class="invalid-feedback"></div>
</div>

<!-- Priority & Status Row -->
<div class="row g-2 mb-3">
    <div class="col-6">
        <label class="form-label required fw-semibold" for="{{ $prefix }}_priority">
            {{ __('Priority') }} <span class="text-danger">*</span>
        </label>
        <select class="form-select select2" id="{{ $prefix }}_priority" name="priority" required>
            @if (isset($priorities))
                @foreach ($priorities as $priority)
                    <option value="{{ $priority->value }}" {{ $priority->value === 'medium' ? 'selected' : '' }}>
                        {{ $priority->label() }}
                    </option>
                @endforeach
            @endif
        </select>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-6">
        <label class="form-label required fw-semibold" for="{{ $prefix }}_task_status">
            {{ __('Status') }} <span class="text-danger">*</span>
        </label>
        <select class="form-select select2" id="{{ $prefix }}_task_status" name="task_status" required>
            @if (isset($statuses))
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" {{ $status->value === 'pending' ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                @endforeach
            @endif
        </select>
        <div class="invalid-feedback"></div>
    </div>
</div>

<!-- Dates Row (Start Date & Due Date) -->
<div class="row g-2 mb-3">
    <div class="col-6">
        <label class="form-label fw-semibold" for="{{ $prefix }}_start_date">{{ __('Start Date') }}</label>
        <input type="text" class="form-control js-flatpickr-date" id="{{ $prefix }}_start_date" name="start_date" placeholder="YYYY-MM-DD" autocomplete="off">
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-6">
        <label class="form-label fw-semibold" for="{{ $prefix }}_completion_date">{{ __('Due Date') }}</label>
        <input type="text" class="form-control js-flatpickr-date" id="{{ $prefix }}_completion_date" name="completion_date" placeholder="YYYY-MM-DD" autocomplete="off">
        <div class="invalid-feedback"></div>
    </div>
</div>

<!-- Description in Arabic -->
<div class="mb-3">
    <label class="form-label fw-semibold" for="{{ $prefix }}_body_ar">
        {{ __('Description / Details [Arabic]') }} <small class="text-muted">({{ __('Optional') }})</small>
    </label>
    <textarea class="form-control" id="{{ $prefix }}_body_ar" name="body_ar" rows="3"
              placeholder="{{ __('أدخل تفاصيل المهمة...') }}" dir="rtl"></textarea>
    <div class="invalid-feedback"></div>
</div>

<!-- Description in English -->
<div class="mb-3">
    <label class="form-label fw-semibold" for="{{ $prefix }}_body_en">
        {{ __('Description / Details [English]') }} <small class="text-muted">({{ __('Optional') }})</small>
    </label>
    <textarea class="form-control" id="{{ $prefix }}_body_en" name="body_en" rows="3"
              placeholder="{{ __('Enter task details in English...') }}" dir="ltr"></textarea>
    <div class="invalid-feedback"></div>
</div>

@if ($prefix === 'edit')
    <!-- Existing Attachments in Edit Form -->
    <div class="mb-3" id="existingAttachmentsContainer" style="display: none;">
        <label class="form-label fw-semibold">{{ __('Current Attachments') }}</label>
        <div class="list-group list-group-flush border rounded" id="existingAttachmentsList"></div>
    </div>
@endif

<!-- File Attachments Upload -->
<div class="mb-3">
    <label class="form-label fw-semibold" for="{{ $prefix }}_attachments">
        {{ $prefix === 'edit' ? __('Add More Attachments') : __('Attachments') }} <small class="text-muted">({{ __('Optional') }})</small>
    </label>
    <input type="file" class="form-control" id="{{ $prefix }}_attachments" name="attachments[]" multiple>
    <div class="form-text small text-muted">{{ __('Allowed formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max 10MB per file)') }}</div>
    <div class="invalid-feedback"></div>
</div>
