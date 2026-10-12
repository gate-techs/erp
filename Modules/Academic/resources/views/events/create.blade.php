@extends('layouts/contentNavbarLayout')

@section('title', __('Add New Event') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('academic.events.index') }}" class="text-muted fw-medium small">{{ __('Events') }}</a></li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Add New Event') }}</li>
            </ol>
        </nav>
        <h4 class="mb-0 fw-bold text-heading d-flex align-items-center gap-2">
            <i class="icon-base bx bx-party text-primary font-22"></i>
            {{ __('Create New Event / Activity') }}
        </h4>
    </div>
    <div class="mt-3 mt-md-0">
        <a href="{{ route('academic.events.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="icon-base bx bx-arrow-back font-18"></i>
            <span>{{ __('Back to Events') }}</span>
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible mb-4" role="alert">
        <h6 class="alert-heading fw-bold mb-1">{{ __('Please review the following errors:') }}</h6>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('academic.events.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Main Information Card -->
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-heading fw-bold">
                        <i class="icon-base bx bx-info-circle text-primary font-20"></i>
                        {{ __('Basic Event Information') }}
                    </h5>
                </div>
                <div class="card-body py-4">
                    <div class="row g-3">
                        <!-- Arabic Name -->
                        <div class="col-12 col-md-6">
                            <label class="form-label required">{{ __('Event Name (Arabic)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_ar" value="{{ old('name_ar') }}" class="form-control @error('name_ar') is-invalid @enderror" placeholder="مثال: رحلة علمية إلى حديقة الشهيد" required>
                            @error('name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- English Name -->
                        <div class="col-12 col-md-6">
                            <label class="form-label required">{{ __('Event Name (English)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control @error('name_en') is-invalid @enderror" placeholder="Example: Science Trip to Al Shaheed Park" required dir="ltr">
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Arabic Description -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">{{ __('Event Details (Arabic)') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                            <textarea name="description_ar" rows="4" class="form-control @error('description_ar') is-invalid @enderror" placeholder="اكتب تفاصيل وبرنامج الفاعلية، الأهداف، والتعليمات لأولياء الأمور...">{{ old('description_ar') }}</textarea>
                            @error('description_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- English Description -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">{{ __('Event Details (English)') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                            <textarea name="description_en" rows="4" class="form-control @error('description_en') is-invalid @enderror" placeholder="Write event schedule, objectives, and instructions for parents..." dir="ltr">{{ old('description_en') }}</textarea>
                            @error('description_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scope & Target Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-heading fw-bold">
                        <i class="icon-base bx bx-buildings text-primary font-20"></i>
                        {{ __('Target Nursery, Branch & Classroom') }}
                    </h5>
                </div>
                <div class="card-body py-4">
                    <div class="row g-3">
                        <!-- Nursery / Institution -->
                        <div class="col-12 col-md-4">
                            <label class="form-label required">{{ __('Nursery / Institution') }} <span class="text-danger">*</span></label>
                            <select id="event_institution_id" name="institution_id" class="select2 form-select @error('institution_id') is-invalid @enderror" required>
                                <option value="">{{ __('Select Nursery') }}</option>
                                @foreach ($institutions as $inst)
                                    <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>
                                        {{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('institution_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Branch -->
                        <div class="col-12 col-md-4">
                            <label class="form-label required">{{ __('Branch') }} <span class="text-danger">*</span></label>
                            <select id="event_branch_id" name="branch_id" class="select2 form-select @error('branch_id') is-invalid @enderror" required>
                                <option value="">{{ __('Select Branch') }}</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->translate(app()->getLocale())?->name ?? $branch->translate('ar')?->name ?? $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Academic Class -->
                        <div class="col-12 col-md-4">
                            <label class="form-label">{{ __('Academic Class') }} <small class="text-muted">({{ __('Optional: Leave empty for all classes') }})</small></label>
                            <select id="event_academic_class_id" name="academic_class_id" class="select2 form-select @error('academic_class_id') is-invalid @enderror">
                                <option value="">{{ __('All Classes in Branch') }}</option>
                                @foreach ($classes as $cls)
                                    <option value="{{ $cls->id }}" {{ old('academic_class_id') == $cls->id ? 'selected' : '' }}>
                                        {{ $cls->translate(app()->getLocale())?->name ?? $cls->translate('ar')?->name ?? $cls->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule & Pricing Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-heading fw-bold">
                        <i class="icon-base bx bx-calendar-event text-primary font-20"></i>
                        {{ __('Schedule, Timing & Pricing') }}
                    </h5>
                </div>
                <div class="card-body py-4">
                    <div class="row g-3">
                        <!-- Start Date -->
                        <div class="col-12 col-md-6">
                            <label class="form-label required">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                            <input type="text" name="start_date" id="event_start_date" value="{{ old('start_date') }}" class="form-control flatpickr-date @error('start_date') is-invalid @enderror" placeholder="YYYY-MM-DD" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">{{ __('End Date') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                            <input type="text" name="end_date" id="event_end_date" value="{{ old('end_date') }}" class="form-control flatpickr-date @error('end_date') is-invalid @enderror" placeholder="YYYY-MM-DD">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Start Time -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">{{ __('Start Time') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                            <input type="text" name="start_time" id="event_start_time" value="{{ old('start_time') }}" class="form-control flatpickr-time @error('start_time') is-invalid @enderror" placeholder="HH:MM">
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- End Time -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">{{ __('End Time') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                            <input type="text" name="end_time" id="event_end_time" value="{{ old('end_time') }}" class="form-control flatpickr-time @error('end_time') is-invalid @enderror" placeholder="HH:MM">
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">{{ __('Price / Fees (KWD)') }} <small class="text-muted">({{ __('Set 0 for free event') }})</small></label>
                            <div class="input-group">
                                <input type="number" step="0.001" min="0" name="price" value="{{ old('price', '0.000') }}" class="form-control @error('price') is-invalid @enderror" placeholder="0.000">
                                <span class="input-group-text">{{ __('KWD') }}</span>
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Event Type -->
                        <div class="col-12 col-md-6">
                            <label class="form-label required">{{ __('Event Type') }} <span class="text-danger">*</span></label>
                            <select name="event_type_id" class="select2 form-select @error('event_type_id') is-invalid @enderror" required>
                                <option value="">{{ __('Select Event Type') }}</option>
                                @foreach ($eventTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('event_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->translate(app()->getLocale())?->name ?? $type->translate('ar')?->name ?? $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('event_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar: Image & Status -->
        <div class="col-12 col-lg-4">
            <!-- Event Cover Image Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-heading fw-bold">
                        <i class="icon-base bx bx-image text-primary font-20"></i>
                        {{ __('Event Image / Banner') }}
                    </h5>
                </div>
                <div class="card-body py-4 text-center">
                    <div class="mb-3">
                        <div id="image_preview_box" class="position-relative d-inline-block rounded border overflow-hidden bg-light" style="width: 100%; height: 200px;">
                            <img id="image_preview" src="" alt="Event Preview" class="w-100 h-100 object-fit-cover d-none">
                            <div id="placeholder_box" class="h-100 d-flex flex-column align-items-center justify-content-center text-muted p-3">
                                <i class="icon-base bx bx-cloud-upload font-36 text-primary mb-2"></i>
                                <span class="fw-medium small">{{ __('Click to upload event image') }}</span>
                                <small class="text-muted mt-1">PNG, JPG, WEBP (Max: 2MB)</small>
                            </div>
                        </div>
                    </div>
                    <div>
                        <input type="file" name="image" id="event_image_input" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback text-start">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Status Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-heading fw-bold">
                        <i class="icon-base bx bx-toggle-right text-primary font-20"></i>
                        {{ __('Publish & Status') }}
                    </h5>
                </div>
                <div class="card-body py-4">
                    <label class="form-label fw-semibold">{{ __('Event Status') }}</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach ($statuses as $key => $lbl)
                            <option value="{{ $key }}" {{ old('status', 'active') === $key ? 'selected' : '' }}>
                                {{ $lbl }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div class="mt-4 pt-2 border-top d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm py-2">
                            <i class="icon-base bx bx-save font-18"></i>
                            <span>{{ __('Save & Publish Event') }}</span>
                        </button>
                        <a href="{{ route('academic.events.index') }}" class="btn btn-outline-secondary">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('page-script')
<script>
window.addEventListener('load', function () {
    const $ = window.jQuery || window.$;
    const branchesUrl = "{{ url('academic/events/branches') }}";
    const classesUrl = "{{ url('academic/events/classes') }}";

    // Select2
    $('.select2').each(function () {
        $(this).select2({
            theme: 'bootstrap-5',
            width: '100%',
            dir: document.documentElement.dir === 'rtl' ? 'rtl' : 'ltr'
        });
    });

    // Flatpickr
    if (window.flatpickr) {
        flatpickr('.flatpickr-date', {
            dateFormat: 'Y-m-d',
            allowInput: true,
            disableMobile: true,
            locale: document.documentElement.dir === 'rtl' && flatpickr.l10ns?.ar ? flatpickr.l10ns.ar : 'default'
        });

        flatpickr('.flatpickr-time', {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            allowInput: true,
            disableMobile: true
        });
    }

    // Dynamic Cascade: Institution -> Branches
    $('#event_institution_id').on('change', function () {
        const instId = $(this).val();
        const branchSelect = $('#event_branch_id');
        branchSelect.empty().append(new Option("{{ __('Select Branch') }}", ""));

        if (!instId) {
            branchSelect.trigger('change');
            return;
        }

        $.getJSON(branchesUrl + '/' + instId, function (data) {
            data.forEach(function (b) {
                branchSelect.append(new Option(b.name, b.id));
            });
            branchSelect.trigger('change');
        });
    });

    // Dynamic Cascade: Branch -> Classes
    $('#event_branch_id').on('change', function () {
        const branchId = $(this).val();
        const classSelect = $('#event_academic_class_id');
        classSelect.empty().append(new Option("{{ __('All Classes in Branch') }}", ""));

        if (!branchId) {
            classSelect.trigger('change');
            return;
        }

        $.getJSON(classesUrl + '/' + branchId, function (data) {
            data.forEach(function (c) {
                classSelect.append(new Option(c.name, c.id));
            });
            classSelect.trigger('change');
        });
    });

    // Image Live Preview
    const imageInput = document.getElementById('event_image_input');
    const imagePreview = document.getElementById('image_preview');
    const placeholderBox = document.getElementById('placeholder_box');

    if (imageInput) {
        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('d-none');
                    placeholderBox.classList.add('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endsection
