@extends('layouts/contentNavbarLayout')

@section('title', __('Customize Print Template') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
/* Simulated Paper Preview */
.preview-container {
    background: #525659;
    padding: 24px;
    border-radius: 12px;
    overflow-x: auto;
    display: flex;
    justify-content: center;
}
.a4-sheet {
    background: #ffffff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    color: #1e293b;
    font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
    position: relative;
    box-sizing: border-box;
    transition: all 0.2s ease;
    width: 210mm;
    min-height: 297mm;
}
.a4-sheet.orientation-landscape {
    width: 297mm;
    min-height: 210mm;
}
.live-header-border {
    border-bottom: 2px solid #2b3548;
    padding-bottom: 12px;
    margin-bottom: 16px;
}
.live-footer-border {
    border-top: 1px solid #cbd5e1;
    padding-top: 10px;
    margin-top: 24px;
}
.signature-box-preview {
    border-top: 1px dashed #64748b;
    padding-top: 6px;
    text-align: center;
    font-size: 11px;
}
.sample-table th {
    background-color: #f1f5f9;
    font-size: 11px;
    font-weight: 700;
    padding: 6px 8px;
    border: 1px solid #cbd5e1;
}
.sample-table td {
    font-size: 11px;
    padding: 6px 8px;
    border: 1px solid #e2e8f0;
}
.card-header .nav-pills .nav-link {
    transition: all 0.2s ease;
    border-radius: 6px;
    font-size: 13px;
    white-space: nowrap;
}
.card-header .nav-pills .nav-link:not(.active):hover {
    background-color: rgba(67, 89, 113, 0.08);
}
@media (max-width: 1400px) {
    .card-header .nav-pills .nav-link {
        font-size: 12px;
        padding: 6px 10px !important;
    }
    .preview-container {
        zoom: 0.85;
    }
}
@media (max-width: 992px) {
    .preview-container {
        zoom: 0.65;
    }
}
</style>
@endsection

@section('content')
<!-- Header with Action Buttons -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-slider text-primary font-24"></i>
            <span>{{ __('Customize Print Template') }}: {{ $template->display_name }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.print-templates.index') }}">{{ __('Print Settings') }}</a></li>
                <li class="breadcrumb-item active">{{ $template->display_name }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('settings.print-templates.preview', $template->id) }}" target="_blank" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-xs">
            <i class="icon-base bx bx-printer font-16"></i>
            <span>{{ __('Live Full Preview') }}</span>
        </a>
        <button type="submit" form="templateEditForm" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4">
            <i class="icon-base bx bx-save font-16"></i>
            <span>{{ __('Save Template') }}</span>
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Settings Form -->
    <div class="col-xxl-7 col-xl-6 col-lg-12">
        <form action="{{ route('settings.print-templates.update', $template->id) }}" method="POST" id="templateEditForm">
            @csrf
            @method('PUT')

            <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                <div class="card-header border-bottom p-2 bg-lighter">
                    <ul class="nav nav-pills flex-wrap gap-1" role="tablist">
                        <li class="nav-item flex-grow-1">
                            <button type="button" class="nav-link active py-2 px-3 w-100 d-flex align-items-center justify-content-center gap-1_5" data-bs-toggle="pill" data-bs-target="#tab-paper" role="tab">
                                <i class="icon-base bx bx-file font-18"></i>
                                <span class="fw-medium font-13">{{ __('Paper & Margins') }}</span>
                            </button>
                        </li>
                        <li class="nav-item flex-grow-1">
                            <button type="button" class="nav-link py-2 px-3 w-100 d-flex align-items-center justify-content-center gap-1_5" data-bs-toggle="pill" data-bs-target="#tab-header" role="tab">
                                <i class="icon-base bx bx-dock-top font-18"></i>
                                <span class="fw-medium font-13">{{ __('Header') }}</span>
                            </button>
                        </li>
                        <li class="nav-item flex-grow-1">
                            <button type="button" class="nav-link py-2 px-3 w-100 d-flex align-items-center justify-content-center gap-1_5" data-bs-toggle="pill" data-bs-target="#tab-footer" role="tab">
                                <i class="icon-base bx bx-dock-bottom font-18"></i>
                                <span class="fw-medium font-13">{{ __('Footer') }}</span>
                            </button>
                        </li>
                        <li class="nav-item flex-grow-1">
                            <button type="button" class="nav-link py-2 px-3 w-100 d-flex align-items-center justify-content-center gap-1_5" data-bs-toggle="pill" data-bs-target="#tab-signatures" role="tab">
                                <i class="icon-base bx bx-edit-alt font-18"></i>
                                <span class="fw-medium font-13">{{ __('Signatures') }}</span>
                            </button>
                        </li>
                        <li class="nav-item flex-grow-1">
                            <button type="button" class="nav-link py-2 px-3 w-100 d-flex align-items-center justify-content-center gap-1_5" data-bs-toggle="pill" data-bs-target="#tab-css" role="tab">
                                <i class="icon-base bx bx-code-alt font-18"></i>
                                <span class="fw-medium font-13">{{ __('Custom CSS') }}</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body pt-4">
                    <div class="tab-content p-0 shadow-none border-0">
                        <!-- Tab 1: Paper & Margins -->
                        <div class="tab-pane fade show active" id="tab-paper" role="tabpanel">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Template Name (English)') }}</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Template Name (Arabic)') }}</label>
                                    <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $template->name_ar) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('Description') }}</label>
                                    <textarea name="description" class="form-control" rows="2">{{ old('description', $template->description) }}</textarea>
                                </div>
                            </div>

                            <h6 class="fw-semibold text-heading mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                                <i class="icon-base bx bx-expand font-18 text-primary"></i>
                                <span>{{ __('Page Setup & Dimensions') }}</span>
                            </h6>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Paper Size') }}</label>
                                    <select name="page_size" id="input_page_size" class="form-select">
                                        <option value="A4" {{ $template->page_size === 'A4' ? 'selected' : '' }}>A4 (210 × 297 mm)</option>
                                        <option value="A5" {{ $template->page_size === 'A5' ? 'selected' : '' }}>A5 (148 × 210 mm)</option>
                                        <option value="Letter" {{ $template->page_size === 'Letter' ? 'selected' : '' }}>Letter (8.5 × 11 in)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Orientation') }}</label>
                                    <select name="orientation" id="input_orientation" class="form-select">
                                        <option value="portrait" {{ $template->orientation === 'portrait' ? 'selected' : '' }}>{{ __('Portrait (طولي)') }}</option>
                                        <option value="landscape" {{ $template->orientation === 'landscape' ? 'selected' : '' }}>{{ __('Landscape (عرضي)') }}</option>
                                    </select>
                                </div>
                            </div>

                            <h6 class="fw-semibold text-heading mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                                <i class="icon-base bx bx-border-outer font-18 text-primary"></i>
                                <span>{{ __('Page Margins (الهوامش)') }}</span>
                            </h6>

                            @php
                                $margins = $template->margins ?? ['top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12, 'unit' => 'mm'];
                            @endphp
                            <div class="row g-3">
                                <div class="col-sm-3 col-6">
                                    <label class="form-label font-12">{{ __('Top Margin') }}</label>
                                    <div class="input-group">
                                        <input type="number" name="margins[top]" id="margin_top" class="form-control" value="{{ $margins['top'] ?? 12 }}" min="0" max="50">
                                        <span class="input-group-text font-11">mm</span>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-6">
                                    <label class="form-label font-12">{{ __('Right Margin') }}</label>
                                    <div class="input-group">
                                        <input type="number" name="margins[right]" id="margin_right" class="form-control" value="{{ $margins['right'] ?? 12 }}" min="0" max="50">
                                        <span class="input-group-text font-11">mm</span>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-6">
                                    <label class="form-label font-12">{{ __('Bottom Margin') }}</label>
                                    <div class="input-group">
                                        <input type="number" name="margins[bottom]" id="margin_bottom" class="form-control" value="{{ $margins['bottom'] ?? 12 }}" min="0" max="50">
                                        <span class="input-group-text font-11">mm</span>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-6">
                                    <label class="form-label font-12">{{ __('Left Margin') }}</label>
                                    <div class="input-group">
                                        <input type="number" name="margins[left]" id="margin_left" class="form-control" value="{{ $margins['left'] ?? 12 }}" min="0" max="50">
                                        <span class="input-group-text font-11">mm</span>
                                    </div>
                                </div>
                                <input type="hidden" name="margins[unit]" value="mm">
                            </div>
                        </div>

                        <!-- Tab 2: Header Settings -->
                        <div class="tab-pane fade" id="tab-header" role="tabpanel">
                            @php $hdr = $template->header_settings ?? []; @endphp

                            <div class="form-check form-switch mb-3 pb-2 border-bottom">
                                <input class="form-check-input" type="checkbox" name="header_settings[show_header]" id="hdr_show_header" value="1" {{ !empty($hdr['show_header']) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="hdr_show_header">{{ __('Enable Header (تفعيل الترويسة العلوية)') }}</label>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="header_settings[show_logo]" id="hdr_show_logo" value="1" {{ !empty($hdr['show_logo']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="hdr_show_logo">{{ __('Show Company Logo') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label font-12">{{ __('Logo Position') }}</label>
                                    <select name="header_settings[logo_position]" id="hdr_logo_position" class="form-select form-select-sm">
                                        <option value="right" {{ ($hdr['logo_position'] ?? '') === 'right' ? 'selected' : '' }}>{{ __('Right (اليمين)') }}</option>
                                        <option value="center" {{ ($hdr['logo_position'] ?? '') === 'center' ? 'selected' : '' }}>{{ __('Center (الوسط)') }}</option>
                                        <option value="left" {{ ($hdr['logo_position'] ?? '') === 'left' ? 'selected' : '' }}>{{ __('Left (اليسار)') }}</option>
                                    </select>
                                </div>
                            </div>

                            <h6 class="fw-semibold text-heading mb-3 pb-2 border-bottom">{{ __('Organization Info to Display') }}</h6>

                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="header_settings[show_company_name]" id="hdr_show_company_name" value="1" {{ !empty($hdr['show_company_name']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="hdr_show_company_name">{{ __('Company / Organization Name') }}</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="header_settings[show_cr]" id="hdr_show_cr" value="1" {{ !empty($hdr['show_cr']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="hdr_show_cr">{{ __('Commercial Registration (السجل التجاري)') }}</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="header_settings[show_tax]" id="hdr_show_tax" value="1" {{ !empty($hdr['show_tax']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="hdr_show_tax">{{ __('Tax / VAT Number (الرقم الضريبي)') }}</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="header_settings[show_phone]" id="hdr_show_phone" value="1" {{ !empty($hdr['show_phone']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="hdr_show_phone">{{ __('Phone Number') }}</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="header_settings[show_email]" id="hdr_show_email" value="1" {{ !empty($hdr['show_email']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="hdr_show_email">{{ __('Email Address') }}</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="header_settings[show_report_title]" id="hdr_show_report_title" value="1" {{ !empty($hdr['show_report_title']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="hdr_show_report_title">{{ __('Report / Document Title') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Footer Settings -->
                        <div class="tab-pane fade" id="tab-footer" role="tabpanel">
                            @php $ftr = $template->footer_settings ?? []; @endphp

                            <div class="form-check form-switch mb-3 pb-2 border-bottom">
                                <input class="form-check-input" type="checkbox" name="footer_settings[show_footer]" id="ftr_show_footer" value="1" {{ !empty($ftr['show_footer']) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="ftr_show_footer">{{ __('Enable Footer (تفعيل التذييل السفلي)') }}</label>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="footer_settings[show_page_number]" id="ftr_show_page_number" value="1" {{ !empty($ftr['show_page_number']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ftr_show_page_number">{{ __('Show Page Numbers (صفحة X من Y)') }}</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="footer_settings[show_timestamp]" id="ftr_show_timestamp" value="1" {{ !empty($ftr['show_timestamp']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ftr_show_timestamp">{{ __('Show Print Date & Time') }}</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="footer_settings[show_printed_by]" id="ftr_show_printed_by" value="1" {{ !empty($ftr['show_printed_by']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ftr_show_printed_by">{{ __('Show Printed By (اسم المستخدم المستخرج)') }}</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="footer_settings[show_qr_code]" id="ftr_show_qr_code" value="1" {{ !empty($ftr['show_qr_code']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ftr_show_qr_code">{{ __('Show Verification QR Code') }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Custom Footer Text / Disclaimer') }}</label>
                                <textarea name="footer_settings[footer_text]" id="ftr_text" class="form-control" rows="2" placeholder="{{ __('e.g. تم إصدار هذا التقرير آلياً...') }}">{{ $ftr['footer_text'] ?? '' }}</textarea>
                            </div>
                        </div>

                        <!-- Tab 4: Signatures -->
                        <div class="tab-pane fade" id="tab-signatures" role="tabpanel">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <h6 class="fw-semibold text-heading mb-0">{{ __('Signature Boxes (خانات التوقيع والاعتماد)') }}</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddSignature">
                                    <i class="icon-base bx bx-plus font-16"></i> {{ __('Add Signature') }}
                                </button>
                            </div>

                            <div id="signaturesContainer" class="d-flex flex-column gap-3">
                                @php $signatures = $template->signature_settings ?? []; @endphp
                                @forelse($signatures as $index => $sig)
                                    <div class="card bg-light border signature-row p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-semibold font-13">{{ __('Signature Box') }} #<span class="sig-index">{{ $index + 1 }}</span></span>
                                            <button type="button" class="btn btn-icon btn-sm text-danger btn-remove-sig">
                                                <i class="icon-base bx bx-trash font-16"></i>
                                            </button>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-5">
                                                <label class="form-label font-11">{{ __('Title (Arabic)') }}</label>
                                                <input type="text" name="signatures[{{ $index }}][title]" class="form-control form-control-sm sig-title" value="{{ $sig['title'] ?? '' }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label font-11">{{ __('Title (English)') }}</label>
                                                <input type="text" name="signatures[{{ $index }}][title_en]" class="form-control form-control-sm" value="{{ $sig['title_en'] ?? '' }}">
                                            </div>
                                            <div class="col-md-3 d-flex align-items-end">
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input sig-show" type="checkbox" name="signatures[{{ $index }}][show]" value="1" {{ !empty($sig['show']) ? 'checked' : '' }}>
                                                    <label class="form-check-label font-12">{{ __('Visible') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted small text-center py-3" id="noSignaturesMsg">{{ __('No signature boxes configured.') }}</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Tab 5: Custom CSS -->
                        <div class="tab-pane fade" id="tab-css" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Custom Print CSS') }}</label>
                                <small class="text-muted d-block mb-2">{{ __('Fine-tune font sizes, table padding, and print media rules for this specific template.') }}</small>
                                <textarea name="custom_css" id="custom_css" class="form-control font-monospace font-12" rows="12">{{ $template->custom_css }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Right Column: Live A4 Preview -->
    <div class="col-xxl-5 col-xl-6 col-lg-12">
        <div class="card shadow-sm border-0 sticky-top" style="top: 85px; z-index: 1;">
            <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-tv font-18 text-primary"></i>
                    <span>{{ __('Live A4 Preview (معاينة حية ومباشرة)') }}</span>
                </h6>
                <span class="badge bg-label-info font-11">
                    <span id="previewPageSize">A4</span> | <span id="previewOrientation">{{ ucfirst($template->orientation) }}</span>
                </span>
            </div>
            <div class="card-body p-0">
                <div class="preview-container">
                    <div class="a4-sheet {{ $template->orientation === 'landscape' ? 'orientation-landscape' : '' }}" id="liveSheet">
                        
                        <!-- Dynamic Custom CSS applied live -->
                        <style id="liveCustomCss">{!! $template->custom_css !!}</style>

                        <!-- Header Preview -->
                        <div id="previewHeader" class="live-header-border">
                            <div class="d-flex align-items-center justify-content-between" id="previewHeaderGrid">
                                <div id="previewHeaderInfo">
                                    <h6 class="fw-bold mb-1" id="previewCompanyName">{{ $globalSetting->site_name ?? 'شركة جيت تك للتطوير التقني' }}</h6>
                                    <div class="font-11 text-muted" id="previewOrgMeta">
                                        <span id="previewCr">س.ت: 1010987654</span>
                                        <span id="previewTax" class="ms-2">| الرقم الضريبي: 300123456700003</span>
                                        <span id="previewPhone" class="ms-2">| هاتف: +966 11 234 5678</span>
                                        <span id="previewEmail" class="ms-2">| info@gatetech.com</span>
                                    </div>
                                </div>
                                <div id="previewHeaderLogo" class="text-center">
                                    <img src="{{ asset('assets/img/branding/logo-dark.png') }}" style="max-height: 50px;" alt="Logo" onerror="this.outerHTML='<div class=\'fw-bold font-13 text-primary\'>[LOGO]</div>'">
                                </div>
                            </div>
                            <div class="text-center mt-2 pt-2 border-top border-light" id="previewDocTitleBox">
                                <h5 class="fw-bold mb-0 text-heading font-14" id="previewDocTitle">{{ $template->display_name }}</h5>
                                <div class="font-11 text-muted">الفترة: من 2026/01/01 إلى 2026/01/31</div>
                            </div>
                        </div>

                        <!-- Body Preview (Sample Data) -->
                        <div class="my-3">
                            <table class="w-100 sample-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px; text-align: center;">#</th>
                                        <th>كود الموظف</th>
                                        <th>اسم الموظف</th>
                                        <th>المسمى الوظيفي</th>
                                        <th>القسم</th>
                                        <th>الفرع</th>
                                        <th style="width: 70px; text-align: center;">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="text-align: center;">1</td>
                                        <td>EMP-001</td>
                                        <td><strong>منى العتيبي</strong></td>
                                        <td>أخصائي موارد بشرية</td>
                                        <td>الموارد البشرية</td>
                                        <td>الإدارة العامة</td>
                                        <td style="text-align: center;"><span class="badge bg-label-success" style="font-size: 9px;">نشط</span></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;">2</td>
                                        <td>EMP-002</td>
                                        <td><strong>أحمد المحمدي</strong></td>
                                        <td>محاسب مالي أول</td>
                                        <td>الشؤون المالية</td>
                                        <td>فرع الرياض</td>
                                        <td style="text-align: center;"><span class="badge bg-label-success" style="font-size: 9px;">نشط</span></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;">3</td>
                                        <td>EMP-003</td>
                                        <td><strong>سارة القحطاني</strong></td>
                                        <td>مشرفة تربوية</td>
                                        <td>التعليم والتدريب</td>
                                        <td>حضانة النجوم</td>
                                        <td style="text-align: center;"><span class="badge bg-label-success" style="font-size: 9px;">نشط</span></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;">4</td>
                                        <td>EMP-004</td>
                                        <td><strong>خالد العلي</strong></td>
                                        <td>مسؤول نظم ومعلومات</td>
                                        <td>تقنية المعلومات</td>
                                        <td>الإدارة العامة</td>
                                        <td style="text-align: center;"><span class="badge bg-label-success" style="font-size: 9px;">نشط</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Signatures Preview -->
                        <div id="previewSignatures" class="row mt-4 pt-3">
                            <!-- Injected dynamically via JS -->
                        </div>

                        <!-- Footer Preview -->
                        <div id="previewFooter" class="live-footer-border">
                            <div class="d-flex align-items-center justify-content-between font-11 text-muted">
                                <div>
                                    <span id="previewPrintedBy">طبع بواسطة: {{ auth()->user()?->name ?? 'User' }}</span>
                                    <span id="previewTimestamp" class="ms-1">| {{ now()->format('Y-m-d H:i') }}</span>
                                </div>
                                <div id="previewFooterText">{{ $template->footer_settings['footer_text'] ?? '' }}</div>
                                <div>
                                    <span id="previewPageNum">صفحة 1 من 1</span>
                                    <i class="icon-base bx bx-qr font-18 align-middle ms-1" id="previewQrIcon"></i>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function toggleEl(id, visible) {
        const el = document.getElementById(id);
        if (el) el.style.display = visible ? '' : 'none';
    }

    function updateLivePreview() {
        const orient = document.getElementById('input_orientation')?.value;
        const size = document.getElementById('input_page_size')?.value || 'A4';
        const sheet = document.getElementById('liveSheet');

        // Dimensions Map
        const dims = {
            'A4': { p: ['210mm', '297mm'], l: ['297mm', '210mm'] },
            'A5': { p: ['148mm', '210mm'], l: ['210mm', '148mm'] },
            'Letter': { p: ['8.5in', '11in'], l: ['11in', '8.5in'] }
        };
        const curDim = dims[size] || dims['A4'];

        if (orient === 'landscape') {
            if (sheet) {
                sheet.classList.add('orientation-landscape');
                sheet.style.width = curDim.l[0];
                sheet.style.minHeight = curDim.l[1];
            }
            const previewOrientation = document.getElementById('previewOrientation');
            if (previewOrientation) previewOrientation.textContent = 'Landscape';
        } else {
            if (sheet) {
                sheet.classList.remove('orientation-landscape');
                sheet.style.width = curDim.p[0];
                sheet.style.minHeight = curDim.p[1];
            }
            const previewOrientation = document.getElementById('previewOrientation');
            if (previewOrientation) previewOrientation.textContent = 'Portrait';
        }

        const previewPageSize = document.getElementById('previewPageSize');
        if (previewPageSize) previewPageSize.textContent = size;

        // Margins
        const mTop = document.getElementById('margin_top')?.value || 12;
        const mRight = document.getElementById('margin_right')?.value || 12;
        const mBottom = document.getElementById('margin_bottom')?.value || 12;
        const mLeft = document.getElementById('margin_left')?.value || 12;
        if (sheet) {
            sheet.style.padding = `${mTop}mm ${mRight}mm ${mBottom}mm ${mLeft}mm`;
        }

        // Header Visibility
        const showHeader = document.getElementById('hdr_show_header')?.checked;
        toggleEl('previewHeader', showHeader);

        // Logo
        const showLogo = document.getElementById('hdr_show_logo')?.checked;
        toggleEl('previewHeaderLogo', showLogo);

        // Logo Position
        const logoPos = document.getElementById('hdr_logo_position')?.value;
        const previewHeaderGrid = document.getElementById('previewHeaderGrid');
        const previewHeaderInfo = document.getElementById('previewHeaderInfo');
        if (previewHeaderGrid && previewHeaderInfo) {
            if (logoPos === 'center') {
                previewHeaderGrid.style.flexDirection = 'column-reverse';
                previewHeaderGrid.style.textAlign = 'center';
                previewHeaderInfo.style.textAlign = 'center';
            } else if (logoPos === 'left') {
                previewHeaderGrid.style.flexDirection = 'row-reverse';
                previewHeaderGrid.style.textAlign = '';
                previewHeaderInfo.style.textAlign = 'right';
            } else {
                previewHeaderGrid.style.flexDirection = 'row';
                previewHeaderGrid.style.textAlign = '';
                previewHeaderInfo.style.textAlign = 'right';
            }
        }

        // Header Elements
        toggleEl('previewCompanyName', document.getElementById('hdr_show_company_name')?.checked);
        toggleEl('previewCr', document.getElementById('hdr_show_cr')?.checked);
        toggleEl('previewTax', document.getElementById('hdr_show_tax')?.checked);
        toggleEl('previewPhone', document.getElementById('hdr_show_phone')?.checked);
        toggleEl('previewEmail', document.getElementById('hdr_show_email')?.checked);

        // Document Title
        const showDocTitle = document.getElementById('hdr_show_report_title')?.checked;
        toggleEl('previewDocTitleBox', showDocTitle);
        const nameArInput = document.querySelector('input[name="name_ar"]');
        const nameInput = document.querySelector('input[name="name"]');
        const nameVal = nameArInput?.value || nameInput?.value || @json($template->display_name);
        const previewDocTitle = document.getElementById('previewDocTitle');
        if (previewDocTitle) previewDocTitle.textContent = nameVal;

        // Signatures preview update
        const sigContainer = document.getElementById('previewSignatures');
        if (sigContainer) {
            sigContainer.innerHTML = '';
            const sigRows = document.querySelectorAll('.signature-row');
            let count = 0;
            sigRows.forEach(function (row) {
                const titleInput = row.querySelector('.sig-title');
                const showCheckbox = row.querySelector('.sig-show');
                const title = titleInput ? titleInput.value.trim() : '';
                const isShow = showCheckbox ? showCheckbox.checked : false;
                if (title && isShow) {
                    count++;
                    const col = document.createElement('div');
                    col.className = 'col text-center';
                    col.innerHTML = `
                        <div class="signature-box-preview">
                            <div class="fw-bold mb-4">${title}</div>
                            <div class="text-muted" style="font-size: 10px;">(التوقيع والاعتماد)</div>
                        </div>
                    `;
                    sigContainer.appendChild(col);
                }
            });
            sigContainer.style.display = count > 0 ? '' : 'none';
        }

        // Footer Visibility
        toggleEl('previewFooter', document.getElementById('ftr_show_footer')?.checked);
        toggleEl('previewPageNum', document.getElementById('ftr_show_page_number')?.checked);
        toggleEl('previewTimestamp', document.getElementById('ftr_show_timestamp')?.checked);
        toggleEl('previewPrintedBy', document.getElementById('ftr_show_printed_by')?.checked);
        toggleEl('previewQrIcon', document.getElementById('ftr_show_qr_code')?.checked);

        const ftrText = document.getElementById('ftr_text')?.value || '';
        const previewFooterText = document.getElementById('previewFooterText');
        if (previewFooterText) previewFooterText.textContent = ftrText;

        // Custom CSS
        const customCss = document.getElementById('custom_css')?.value || '';
        const liveCustomCss = document.getElementById('liveCustomCss');
        if (liveCustomCss) liveCustomCss.textContent = customCss;
    }

    // Trigger instantly on any input, select, change, keyup or paste
    const form = document.getElementById('templateEditForm');
    if (form) {
        ['input', 'change', 'keyup', 'paste'].forEach(function (evt) {
            form.addEventListener(evt, updateLivePreview);
        });
    }

    // Add Signature Row
    const btnAddSig = document.getElementById('btnAddSignature');
    if (btnAddSig) {
        btnAddSig.addEventListener('click', function () {
            const noMsg = document.getElementById('noSignaturesMsg');
            if (noMsg) noMsg.remove();
            const index = document.querySelectorAll('.signature-row').length;
            const newRow = document.createElement('div');
            newRow.className = 'card bg-light border signature-row p-3';
            newRow.innerHTML = `
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-semibold font-13">{{ __('Signature Box') }} #<span class="sig-index">${index + 1}</span></span>
                    <button type="button" class="btn btn-icon btn-sm text-danger btn-remove-sig">
                        <i class="icon-base bx bx-trash font-16"></i>
                    </button>
                </div>
                <div class="row g-2">
                    <div class="col-md-5">
                        <label class="form-label font-11">{{ __('Title (Arabic)') }}</label>
                        <input type="text" name="signatures[${index}][title]" class="form-control form-control-sm sig-title" value="توقيع جديد">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label font-11">{{ __('Title (English)') }}</label>
                        <input type="text" name="signatures[${index}][title_en]" class="form-control form-control-sm" value="New Signature">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check mb-1">
                            <input class="form-check-input sig-show" type="checkbox" name="signatures[${index}][show]" value="1" checked>
                            <label class="form-check-label font-12">{{ __('Visible') }}</label>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('signaturesContainer')?.appendChild(newRow);
            updateLivePreview();
        });
    }

    // Remove Signature Row
    document.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.btn-remove-sig');
        if (removeBtn) {
            const row = removeBtn.closest('.signature-row');
            if (row) {
                row.remove();
                document.querySelectorAll('.signature-row').forEach(function (r, idx) {
                    const idxSpan = r.querySelector('.sig-index');
                    if (idxSpan) idxSpan.textContent = idx + 1;
                });
                updateLivePreview();
            }
        }
    });

    // Initial run on page load
    updateLivePreview();
});
</script>
@endsection
