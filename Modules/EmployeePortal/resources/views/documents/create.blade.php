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

    <!-- 3. Form Submission -->
    <form action="{{ route('portal.documents.upload') }}" method="POST" enctype="multipart/form-data" id="portalDocumentForm">
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
            <select name="document_type_id" id="portalDocTypeSelect" class="form-select select2 select2-hidden-accessible" style="width: 100%;" required>
                <option value=""></option>
                @foreach($documentTypes as $type)
                    <option value="{{ $type->id }}" {{ ($document?->document_type_id == $type->id || old('document_type_id') == $type->id) ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
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
                   class="portal-create-doc-input" 
                   value="{{ $document?->notes ?? old('issuing_authority') }}" 
                   required />
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
                   class="portal-create-doc-input" 
                   value="{{ $document?->document_number ?? old('document_number') }}" />
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
                <input type="date" 
                       name="expiry_date" 
                       id="docExpiryDateInput" 
                       class="portal-create-doc-date-input" 
                       value="{{ $document?->expiry_date ? $document->expiry_date->format('Y-m-d') : old('expiry_date') }}" />
                <i class="bx bx-calendar portal-create-doc-date-icon"></i>
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
                    <span class="text-danger">*</span>
                </label>
            </div>

            <div class="portal-create-doc-dropzone" onclick="document.getElementById('docFileInput').click()">
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
                       onchange="handleFileSelected(this)" 
                       {{ $isEdit ? '' : 'required' }} />
            </div>
        </div>

        <!-- زر الإرسال -->
        <div class="portal-create-doc-submit-wrap">
            <button type="submit" class="portal-create-doc-submit-btn">
                <span>{{ $isEdit ? __('Update Document') : __('Submit Document') }}</span>
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
    $(document).ready(function() {
        if ($.fn.select2) {
            $('#portalDocTypeSelect').select2({
                width: '100%',
                dir: '{{ app()->getLocale() === "ar" ? "rtl" : "ltr" }}'
            });
        }

        const isPerm = document.getElementById('isPermanentCheckbox');
        if (isPerm && isPerm.checked) {
            togglePermanent(true);
        }
    });

    function togglePermanent(isPermanent) {
        const dateInput = document.getElementById('docExpiryDateInput');
        if (dateInput) {
            dateInput.disabled = isPermanent;
            if (isPermanent) {
                dateInput.value = '';
            }
        }
    }

    function handleFileSelected(input) {
        const displayBox = document.getElementById('pickedFileDisplay');
        const nameSpan = document.getElementById('pickedFileName');
        if (input.files && input.files[0]) {
            nameSpan.textContent = input.files[0].name;
            displayBox.style.display = 'flex';
        } else {
            displayBox.style.display = 'none';
        }
    }
</script>
@endpush
