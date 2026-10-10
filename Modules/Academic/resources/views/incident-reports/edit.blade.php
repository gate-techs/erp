@extends('layouts/contentNavbarLayout')

@section('title', __('Edit Incident Report') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('academic.incident-reports.index') }}" class="text-muted fw-medium small">{{ __('Incident Reports') }}</a></li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Edit Incident Report') }} #{{ $report->id }}</li>
            </ol>
        </nav>
        <h4 class="mb-0 fw-bold text-heading">{{ __('Edit Incident Report') }} #{{ $report->id }}</h4>
    </div>

    <div>
        <a href="{{ route('academic.incident-reports.index') }}" class="btn btn-label-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i>{{ __('Back to List') }}
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Form Card -->
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('academic.incident-reports.update', $report->id) }}" method="POST" id="formEditIncidentReport">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <!-- 1. الطالب -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading required" for="student_id">{{ __('Student') }}</label>
                    <select name="student_id" id="student_id" class="select2 form-select" required>
                        <option value="">{{ __('Select Student...') }}</option>
                        @foreach ($students as $student)
                            @php
                                $sName = $student->translate(app()->getLocale())?->name ?? $student->translate('ar')?->name ?? $student->name;
                                $cName = $student->class?->translate(app()->getLocale())?->name ?? $student->class?->name ?? '-';
                                $code = $student->student_code ? " ({$student->student_code})" : '';
                            @endphp
                            <option value="{{ $student->id }}" {{ old('student_id', $report->student_id) == $student->id ? 'selected' : '' }}>
                                {{ $sName }}{{ $code }} - {{ $cName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. نوع الحادث -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading" for="incident_type_id">{{ __('Incident / Injury Type') }}</label>
                    <select name="incident_type_id" id="incident_type_id" class="select2 form-select">
                        <option value="">{{ __('Select Incident Type...') }}</option>
                        @foreach ($incidentTypes as $type)
                            <option value="{{ $type->id }}" {{ old('incident_type_id', $report->incident_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->translate(app()->getLocale())?->name ?? $type->translate('ar')?->name ?? $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. تاريخ الحادث -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading required" for="date">{{ __('Incident Date') }}</label>
                    <input type="text" name="date" id="date" class="form-control js-flatpickr-date flatpickr-input active" value="{{ old('date', $report->date ? $report->date->format('Y-m-d') : '') }}" placeholder="YYYY-MM-DD" autocomplete="off" required>
                </div>

                <!-- 4. الوقت -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading" for="time">{{ __('Time') }}</label>
                    <input type="time" name="time" id="time" class="form-control" value="{{ old('time', $report->time) }}">
                </div>

                <!-- 5. المكان -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading" for="location">{{ __('Location') }}</label>
                    <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $report->location) }}" placeholder="{{ __('Location') }}...">
                </div>

                <!-- 6. المسؤول -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading" for="user_id">{{ __('Responsible Officer') }}</label>
                    <select name="user_id" id="user_id" class="select2 form-select">
                        <option value="">{{ __('Select Responsible Officer...') }}</option>
                        @foreach ($staffUsers as $staff)
                            <option value="{{ $staff->id }}" {{ old('user_id', $report->user_id) == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 7. الشهود الحاضرون -->
                @php
                    $currentPersons = is_array(old('persons')) ? old('persons') : (is_array($report->persons) ? $report->persons : []);
                @endphp
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading" for="persons">{{ __('Witnesses / Present Staff') }}</label>
                    <select name="persons[]" id="persons" class="select2 form-select" multiple="multiple" data-placeholder="{{ __('Select Witnesses...') }}">
                        @foreach ($staffUsers as $staff)
                            <option value="{{ $staff->id }}" {{ in_array($staff->id, $currentPersons) ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 8. وصف الطالب / الواقعة -->
                <div class="col-12">
                    <label class="form-label fw-semibold text-heading" for="student_description">{{ __('Student Description') }}</label>
                    <textarea name="student_description" id="student_description" rows="3" class="form-control" placeholder="{{ __('Student Description') }}...">{{ old('student_description', $report->student_description) }}</textarea>
                </div>

                <!-- 9. الإجراءات المتخذة -->
                <div class="col-12">
                    <label class="form-label fw-semibold text-heading" for="procedures">{{ __('Procedures Taken') }}</label>
                    <textarea name="procedures" id="procedures" rows="3" class="form-control" placeholder="{{ __('Procedures Taken') }}...">{{ old('procedures', $report->procedures) }}</textarea>
                </div>

                <!-- 10. تقرير الممرضة -->
                <div class="col-12">
                    <label class="form-label fw-semibold text-heading" for="nurse_report">{{ __('Nurse Report') }}</label>
                    <textarea name="nurse_report" id="nurse_report" rows="3" class="form-control" placeholder="{{ __('Nurse Report') }}...">{{ old('nurse_report', $report->nurse_report) }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="col-12 d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                    <a href="{{ route('academic.incident-reports.index') }}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary px-4">{{ __('Update Report') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Select2
    if (window.jQuery && jQuery().select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dir: document.documentElement.dir === 'rtl' ? 'rtl' : 'ltr'
        });
    }

    // Flatpickr
    if (typeof window.flatpickr !== 'undefined') {
        $('.js-flatpickr-date').flatpickr({
            dateFormat: 'Y-m-d',
            allowInput: true
        });
    }
});
</script>
@endsection
