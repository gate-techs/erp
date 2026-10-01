@php($prefix = $prefix ?? 'req')

<div class="row g-3">
    <!-- Customer / Requester Info -->
    <div class="col-12">
        <h6 class="text-muted text-uppercase fs-7 fw-bold mb-1 border-bottom pb-2 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-user text-primary"></i>
            <span>{{ __('Customer / Requester Info') }}</span>
        </h6>
    </div>

    <div class="col-12 col-md-6" id="{{ $prefix }}_civil_id_container">
        <div class="d-flex align-items-center justify-content-between mb-1">
            <label for="{{ $prefix }}_civil_id" class="form-label fw-semibold mb-0">
                {{ __('Civil ID') }} <span class="text-danger">*</span>
            </label>
            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none btn-switch-mode" 
                data-prefix="{{ $prefix }}" 
                id="{{ $prefix }}_switch_mode_btn" 
                style="font-size: 0.8rem; display: none;">
                <i class="icon-base bx bx-edit-alt me-1"></i><span>{{ __('Manual Input') }}</span>
            </button>
        </div>

        {{-- Input Mode (Default, or when not found, or when switched to manual) --}}
        <div id="{{ $prefix }}_input_wrapper">
            <input type="text" 
                id="{{ $prefix }}_civil_id" 
                name="civil_id" 
                class="form-control civil-id-input" 
                value="{{ old('civil_id', $serviceRequest?->civil_id) }}" 
                required 
                dir="ltr" 
                placeholder="{{ __('Enter Civil ID to search or add...') }}" 
                autocomplete="off" 
                data-prefix="{{ $prefix }}">
        </div>

        {{-- Select Mode (Activated when search matches are found) --}}
        <div id="{{ $prefix }}_select_wrapper" style="display: none;">
            <select id="{{ $prefix }}_civil_id_select" class="form-select select2 civil-id-select" data-placeholder="{{ __('Select from matched records...') }}" data-prefix="{{ $prefix }}">
                <option value="">{{ __('Select from matched records...') }}</option>
            </select>
        </div>

        <div id="{{ $prefix }}_civil_id_feedback" class="mt-1" style="display: none;"></div>
    </div>

    <div class="col-12 col-md-6">
        <label for="{{ $prefix }}_name" class="form-label fw-semibold">{{ __('Name') }} <span class="text-danger">*</span></label>
        <input type="text" id="{{ $prefix }}_name" name="name" class="form-control" value="{{ old('name', $serviceRequest?->name) }}" required placeholder="{{ __('Name') }}">
    </div>

    <div class="col-12">
        <label for="{{ $prefix }}_email" class="form-label fw-semibold">{{ __('Email') }}</label>
        <input type="email" id="{{ $prefix }}_email" name="email" class="form-control" value="{{ old('email', $serviceRequest?->email) }}" dir="ltr" placeholder="name@example.com">
    </div>

    <!-- Request Details -->
    <div class="col-12 pt-2">
        <h6 class="text-muted text-uppercase fs-7 fw-bold mb-1 border-bottom pb-2 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-detail text-primary"></i>
            <span>{{ __('Request Information') }}</span>
        </h6>
    </div>

    <div class="col-12 col-md-6">
        <label for="{{ $prefix }}_type" class="form-label fw-semibold">{{ __('Type') }} <span class="text-danger">*</span></label>
        <select id="{{ $prefix }}_type" name="type" class="form-select select2" required>
            <option value="">{{ __('Select Type') }}</option>
            @foreach ($types as $type)
                <option value="{{ $type->value }}" @selected(old('type', $serviceRequest?->type?->value) === $type->value)>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label for="{{ $prefix }}_status" class="form-label fw-semibold">{{ __('Status') }}</label>
        <select id="{{ $prefix }}_status" name="status" class="form-select select2">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $serviceRequest?->status?->value ?? 'pending') === $status->value)>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label for="{{ $prefix }}_branch_id" class="form-label fw-semibold">{{ __('Branch') }}</label>
        <select id="{{ $prefix }}_branch_id" name="branch_id" class="form-select select2">
            <option value="">{{ __('Select Branch') }}</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected(old('branch_id', $serviceRequest?->branch_id) == $branch->id)>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label for="{{ $prefix }}_department_id" class="form-label fw-semibold">{{ __('Department') }}</label>
        <select id="{{ $prefix }}_department_id" name="department_id" class="form-select select2">
            <option value="">{{ __('Select Department') }}</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}" @selected(old('department_id', $serviceRequest?->department_id) == $department->id)>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <label for="{{ $prefix }}_call_recording_number" class="form-label fw-semibold">{{ __('Call Recording Number') }}</label>
        <input type="text" id="{{ $prefix }}_call_recording_number" name="call_recording_number" class="form-control" value="{{ old('call_recording_number', $serviceRequest?->call_recording_number) }}" dir="ltr" placeholder="{{ __('Call Recording Number') }}">
    </div>

    <div class="col-12">
        <label for="{{ $prefix }}_notes" class="form-label fw-semibold">{{ __('Notes') }}</label>
        <textarea id="{{ $prefix }}_notes" name="notes" class="form-control" rows="3" placeholder="{{ __('Notes') }}...">{{ old('notes', $serviceRequest?->notes) }}</textarea>
    </div>

    <div class="col-12">
        <label for="{{ $prefix }}_image" class="form-label fw-semibold">{{ __('Image') }}</label>
        <input type="file" id="{{ $prefix }}_image" name="image" class="form-control" accept="image/*">
        <div id="{{ $prefix }}_image_preview_box" class="mt-2 d-none">
            <p class="mb-1 text-muted fs-8">{{ __('Current Image:') }}</p>
            <a id="{{ $prefix }}_image_preview_link" href="#" target="_blank">
                <img id="{{ $prefix }}_image_preview" src="" alt="preview" class="rounded border p-1" style="max-height: 90px; max-width: 140px; object-fit: cover;">
            </a>
        </div>
    </div>

    <div class="col-12 mb-2">
        <label for="{{ $prefix }}_attachments" class="form-label fw-semibold">{{ __('Attachments') }}</label>
        <input type="file" id="{{ $prefix }}_attachments" name="attachments[]" class="form-control" multiple>
    </div>
</div>
