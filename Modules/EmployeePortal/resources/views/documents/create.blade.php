@extends('employeeportal::layouts.master')

@section('title', $isEdit ? __('Edit Document') : __('New Document'))

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="portal-create-doc-container">
    <!-- 1. Top Navigation Header -->
    <div class="portal-docs-nav-header">
        <a href="{{ route('portal.documents.index') }}" class="portal-docs-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-right' : 'bx-chevron-left' }}"></i>
        </a>
        <h1 class="portal-docs-nav-title">{{ $isEdit ? __('Edit Document') : __('New Document') }}</h1>
        <div class="portal-docs-nav-placeholder"></div>
    </div>

    <!-- 2. Instructions Banner Card (إرشادات التحقق والاعتماد) -->
    <div class="portal-create-doc-info-card">
        <div class="portal-create-doc-info-body">
            <h2 class="portal-create-doc-info-title">{{ __('Verification and Accreditation Guidelines') }}</h2>
            <p class="portal-create-doc-info-desc">
                {{ __('Please ensure image clarity and that entered data matches official accredited documents issued within the State of Kuwait.') }}
            </p>
        </div>
        <div class="portal-create-doc-info-icon">
            <i class="bx bx-check-shield"></i>
        </div>
    </div>

    <!-- Server-Side Error Alert Banner -->
    @if(session('error'))
        <div class="portal-form-alert-banner">
            <div class="alert-icon"><i class="bx bx-error"></i></div>
            <div class="alert-text">{{ session('error') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="portal-form-alert-banner">
            <div class="alert-icon"><i class="bx bx-error-circle"></i></div>
            <div class="alert-text">
                {{ __('Please correct the highlighted errors before submitting the document.') }}
            </div>
        </div>
    @endif

    <!-- 3. Form Submission -->
    <form action="{{ route('portal.documents.upload') }}" method="POST" enctype="multipart/form-data" id="portalDocumentForm" novalidate onsubmit="return validateDocumentForm(event)">
        @csrf
        <input type="hidden" name="document_id" value="{{ $document?->id ?? '' }}" />

        <!-- الحقل الأول: نوع المستند (Dynamic from HR Settings with Select2) -->
        <div class="portal-create-doc-group">
            <div class="portal-create-doc-label-row">
                <label for="portalDocTypeSelect" class="portal-create-doc-label">
                    <i class="bx bx-folder"></i>
                    <span>{{ __('Document Type') }}</span>
                    <span class="text-danger">*</span>
                </label>
            </div>
            <select name="document_type_id" id="portalDocTypeSelect" class="form-select select2 select2-hidden-accessible @error('document_type_id') is-invalid @enderror" style="width: 100%;">
                <option value=""></option>
                @foreach($documentTypes as $type)
                    <option value="{{ $type->id }}" {{ ($document?->document_type_id == $type->id || old('document_type_id') == $type->id) ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
            <div class="portal-invalid-feedback" id="docTypeError">
                @error('document_type_id') {{ $message }} @enderror
            </div>
        </div>

        <!-- الحقل الثاني: اسم الوثيقة / جهة الإصدار -->
        <div class="portal-create-doc-group">
            <div class="portal-create-doc-label-row">
                <label for="issuingAuthorityInput" class="portal-create-doc-label">
                    <i class="bx bxs-institution"></i>
                    <span>{{ __('Document Name / Issuing Authority') }}</span>
                    <span class="text-danger">*</span>
                </label>
            </div>
            <input type="text" 
                   name="issuing_authority" 
                   id="issuingAuthorityInput" 
                   class="portal-create-doc-input @error('issuing_authority') is-invalid @enderror" 
                   value="{{ old('issuing_authority', $document?->notes ?? '') }}" 
                   placeholder="{{ __('e.g. Ministry of Interior, Public Authority for Civil Information...') }}" />
            <div class="portal-invalid-feedback" id="issuingAuthError">
                @error('issuing_authority') {{ $message }} @enderror
            </div>
        </div>

        <!-- الحقل الثالث: رقم الوثيقة أو كود الاعتماد (اختياري) -->
        <div class="portal-create-doc-group">
            <div class="portal-create-doc-label-row">
                <label for="documentNumberInput" class="portal-create-doc-label">
                    <i class="bx bx-hash"></i>
                    <span>{{ __('Document Number or Accreditation Code') }}</span>
                </label>
                <span class="portal-create-doc-optional">{{ __('Optional') }}</span>
            </div>
            <input type="text" 
                   name="document_number" 
                   id="documentNumberInput" 
                   class="portal-create-doc-input @error('document_number') is-invalid @enderror" 
                   value="{{ old('document_number', $document?->document_number ?? '') }}" 
                   placeholder="{{ __('e.g. DOC-849201') }}" />
            <div class="portal-invalid-feedback" id="docNumberError">
                @error('document_number') {{ $message }} @enderror
            </div>
        </div>

        <!-- الحقل الرابع: تاريخ انتهاء الصلاحية -->
        <div class="portal-create-doc-expiry-card">
            <div class="portal-create-doc-label-row">
                <span class="portal-create-doc-label">
                    <i class="bx bx-calendar"></i>
                    <span>{{ __('Expiry Date') }}</span>
                </span>
            </div>

            <div class="portal-create-doc-date-wrap">
                <input type="text" 
                       name="expiry_date" 
                       id="docExpiryDateInput" 
                       class="form-control js-flatpickr-date flatpickr-input flatpickr-custom-input active portal-create-doc-date-input @error('expiry_date') is-invalid @enderror" 
                       value="{{ old('expiry_date', $document?->expiry_date ? $document->expiry_date->format('Y-m-d') : '') }}" 
                       placeholder="YYYY-MM-DD"
                       autocomplete="off" />
                <i class="bx bx-calendar portal-create-doc-date-icon"></i>
            </div>
            <div class="portal-invalid-feedback" id="expiryDateError">
                @error('expiry_date') {{ $message }} @enderror
            </div>

            <label class="portal-create-doc-checkbox-row">
                <input type="checkbox" 
                       name="is_permanent" 
                       id="isPermanentCheckbox" 
                       value="1" 
                       onchange="togglePermanent(this.checked)" 
                       {{ ($document && empty($document->expiry_date)) || old('is_permanent') ? 'checked' : '' }} />
                <span class="portal-create-doc-checkbox-label">
                    {{ __('Permanent document with no expiration (e.g. university degrees and training courses)') }}
                </span>
            </label>
        </div>

        <!-- الحقل الخامس: ملف الوثيقة أو المسح الضوئي -->
        <div class="portal-create-doc-group">
            <div class="portal-create-doc-label-row">
                <label class="portal-create-doc-label">
                    <i class="bx bx-cloud-upload"></i>
                    <span>{{ __('Document File or Scan') }}</span>
                    @if(!$isEdit)
                    <span class="text-danger">*</span>
                    @endif
                </label>
            </div>

            <div class="portal-create-doc-dropzone @error('file') is-invalid @enderror" id="docDropzoneBox" onclick="document.getElementById('docFileInput').click()">
                <div class="portal-create-doc-dropzone-icon">
                    <i class="bx bx-cloud-upload"></i>
                </div>
                <div class="portal-create-doc-dropzone-title">{{ __('Click here to upload file or capture image') }}</div>
                <div class="portal-create-doc-dropzone-subtitle">{{ __('Maximum 5 MB, JPG/PNG or PDF') }}</div>

                <div id="pickedFileDisplay" class="portal-create-doc-picked-file" style="display: none;">
                    <span id="pickedFileName"></span>
                    <i class="bx bx-check-circle"></i>
                </div>

                @if($isEdit && $document?->file_name)
                <div class="portal-create-doc-picked-file" style="margin-top: 8px;">
                    <span>{{ $document->file_name }}</span>
                    <i class="bx bx-file"></i>
                </div>
                @endif

                <input type="file" 
                       name="file" 
                       id="docFileInput" 
                       accept=".pdf,image/png,image/jpeg,image/webp" 
                       style="display: none;" 
                       onchange="handleFileSelected(this)" />
            </div>
            <div class="portal-invalid-feedback" id="fileError">
                @error('file') {{ $message }} @enderror
            </div>
        </div>

        <!-- زر الإرسال -->
        <div class="portal-create-doc-submit-wrap">
            <button type="submit" class="portal-create-doc-submit-btn" id="btnSubmitDoc">
                <span>{{ $isEdit ? __('Update Document') : __('Submit Document') }}</span>
                <i class="bx bx-check-circle fs-5"></i>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- jQuery & Select2 JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    const isEditMode = {{ $isEdit ? 'true' : 'false' }};

    $(document).ready(function() {
        if ($.fn.select2) {
            $('#portalDocTypeSelect').select2({
                width: '100%',
                placeholder: "{{ __('Select Document Type...') }}",
                allowClear: true,
                dir: '{{ app()->getLocale() === "ar" ? "rtl" : "ltr" }}'
            }).on('change', function() {
                clearFieldError('portalDocTypeSelect', 'docTypeError');
                const s2Container = $(this).next('.select2-container');
                s2Container.removeClass('is-invalid');
            });
        }

        if (typeof flatpickr !== 'undefined') {
            const expiryEl = document.getElementById('docExpiryDateInput');
            if (expiryEl && !expiryEl._flatpickr) {
                flatpickr(expiryEl, {
                    locale: '{{ app()->getLocale() === "ar" ? "ar" : "en" }}',
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    disableMobile: true,
                    onChange: function() {
                        clearFieldError('docExpiryDateInput', 'expiryDateError');
                    }
                });
            }
        }

        const isPerm = document.getElementById('isPermanentCheckbox');
        if (isPerm && isPerm.checked) {
            togglePermanent(true);
        }

        // Live input error clearing
        const authInput = document.getElementById('issuingAuthorityInput');
        if (authInput) {
            authInput.addEventListener('input', () => clearFieldError('issuingAuthorityInput', 'issuingAuthError'));
        }
    });

    function setFieldError(fieldId, errorId, message) {
        const field = document.getElementById(fieldId);
        const errorEl = document.getElementById(errorId);
        if (field) {
            field.classList.add('is-invalid');
            if (field.id === 'portalDocTypeSelect') {
                $(field).next('.select2-container').addClass('is-invalid');
            }
        }
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.add('d-block');
        }
    }

    function clearFieldError(fieldId, errorId) {
        const field = document.getElementById(fieldId);
        const errorEl = document.getElementById(errorId);
        if (field) {
            field.classList.remove('is-invalid');
            if (field.id === 'portalDocTypeSelect') {
                $(field).next('.select2-container').removeClass('is-invalid');
            }
        }
        if (errorEl) {
            errorEl.textContent = '';
            errorEl.classList.remove('d-block');
        }
    }

    function togglePermanent(isPermanent) {
        const dateInput = document.getElementById('docExpiryDateInput');
        if (dateInput) {
            dateInput.disabled = isPermanent;
            if (isPermanent) {
                clearFieldError('docExpiryDateInput', 'expiryDateError');
                if (dateInput._flatpickr) {
                    dateInput._flatpickr.clear();
                } else {
                    dateInput.value = '';
                }
            }
        }
    }

    function handleFileSelected(input) {
        clearFieldError('docDropzoneBox', 'fileError');
        const displayBox = document.getElementById('pickedFileDisplay');
        const nameSpan = document.getElementById('pickedFileName');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxBytes = 5 * 1024 * 1024; // 5MB
            if (file.size > maxBytes) {
                setFieldError('docDropzoneBox', 'fileError', "{{ __('File size exceeds 5MB limit!') }}");
                input.value = '';
                displayBox.style.display = 'none';
                return;
            }
            nameSpan.textContent = file.name;
            displayBox.style.display = 'flex';
        } else {
            displayBox.style.display = 'none';
        }
    }

    function validateDocumentForm(e) {
        let hasError = false;

        const docType = document.getElementById('portalDocTypeSelect');
        const issuingAuth = document.getElementById('issuingAuthorityInput');
        const isPerm = document.getElementById('isPermanentCheckbox');
        const expiryDate = document.getElementById('docExpiryDateInput');
        const fileInput = document.getElementById('docFileInput');

        // 1. Validate Document Type
        if (!docType || !docType.value) {
            setFieldError('portalDocTypeSelect', 'docTypeError', "{{ __('Please select the document type.') }}");
            hasError = true;
        } else {
            clearFieldError('portalDocTypeSelect', 'docTypeError');
        }

        // 2. Validate Issuing Authority
        if (!issuingAuth || !issuingAuth.value.trim()) {
            setFieldError('issuingAuthorityInput', 'issuingAuthError', "{{ __('Please enter document name or issuing authority.') }}");
            hasError = true;
        } else {
            clearFieldError('issuingAuthorityInput', 'issuingAuthError');
        }

        // 3. Validate Expiry Date if not permanent
        if (isPerm && !isPerm.checked) {
            if (!expiryDate || !expiryDate.value.trim()) {
                setFieldError('docExpiryDateInput', 'expiryDateError', "{{ __('Please specify the document expiry date or mark as permanent.') }}");
                hasError = true;
            } else {
                clearFieldError('docExpiryDateInput', 'expiryDateError');
            }
        }

        // 4. Validate File Attachment (Required on create, optional on edit)
        if (!isEditMode && (!fileInput || !fileInput.files || fileInput.files.length === 0)) {
            setFieldError('docDropzoneBox', 'fileError', "{{ __('Please upload the document file or image.') }}");
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            if (typeof showPortalToast === 'function') {
                showPortalToast('error', "{{ __('Required Fields Missing') }}", "{{ __('Please fill all mandatory fields marked in red.') }}");
            }
            return false;
        }

        // Prevent double submit
        const submitBtn = document.getElementById('btnSubmitDoc');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span>{{ __('Submitting...') }}</span> <i class="bx bx-loader-alt bx-spin fs-5"></i>`;
        }

        return true;
    }
</script>
@endpush
