@php($prefix = $prefix ?? 'lead')

<div class="mb-3">
    <label for="{{ $prefix }}_name" class="form-label fw-semibold">{{ __('Name') }} <span class="text-danger">*</span></label>
    <input type="text" id="{{ $prefix }}_name" name="name" class="form-control" value="{{ old('name', $lead?->name) }}" required>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}_phone" class="form-label fw-semibold">{{ __('Phone') }} <span class="text-danger">*</span></label>
    <input type="text" id="{{ $prefix }}_phone" name="phone" class="form-control" value="{{ old('phone', $lead?->phone) }}" required dir="ltr">
</div>

<div class="mb-3">
    <label for="{{ $prefix }}_email" class="form-label fw-semibold">{{ __('Email') }}</label>
    <input type="email" id="{{ $prefix }}_email" name="email" class="form-control" value="{{ old('email', $lead?->email) }}" dir="ltr">
</div>

<div class="mb-3">
    <label for="{{ $prefix }}_civil_id" class="form-label fw-semibold">{{ __('Civil ID') }}</label>
    <input type="text" id="{{ $prefix }}_civil_id" name="civil_id" class="form-control" value="{{ old('civil_id', $lead?->civil_id) }}" dir="ltr">
</div>

<div class="mb-3">
    <label for="{{ $prefix }}_job_title" class="form-label fw-semibold">{{ __('Job Title') }}</label>
    <input type="text" id="{{ $prefix }}_job_title" name="job_title" class="form-control" value="{{ old('job_title', $lead?->job_title) }}">
</div>

<div class="mb-3">
    <label for="{{ $prefix }}_address" class="form-label fw-semibold">{{ __('Address') }}</label>
    <textarea id="{{ $prefix }}_address" name="address" class="form-control" rows="2">{{ old('address', $lead?->address) }}</textarea>
</div>

<div class="mb-4">
    <label for="{{ $prefix }}_notes" class="form-label fw-semibold">{{ __('Notes') }}</label>
    <textarea id="{{ $prefix }}_notes" name="notes" class="form-control" rows="3">{{ old('notes', $lead?->notes) }}</textarea>
</div>
