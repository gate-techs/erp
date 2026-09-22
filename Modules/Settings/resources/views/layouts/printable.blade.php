<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $template->display_name ?? __('Print Document')) - {{ config('app.name', 'ERP') }}</title>
    
    <!-- Google Fonts Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Boxicons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    @php
        $margins = $template->margins ?? ['top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12, 'unit' => 'mm'];
        $unit = $margins['unit'] ?? 'mm';
        $orient = request('orientation', $template->orientation ?: 'portrait');
        $size = $template->page_size ?: 'A4';
        $hdr = $template->header_settings ?? [];
        $ftr = $template->footer_settings ?? [];
        $signatures = array_filter($template->signature_settings ?? [], fn($s) => !empty($s['show']));
    @endphp

    <style>
        @page {
            size: {{ $size }} {{ $orient }};
            margin: {{ $margins['top'] }}{{ $unit }} {{ $margins['right'] }}{{ $unit }} {{ $margins['bottom'] }}{{ $unit }} {{ $margins['left'] }}{{ $unit }};
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
            background: #eef2f6;
            color: #1e293b;
            font-size: 12px;
            line-height: 1.5;
        }
        
        /* Preview Toolbar (Hidden on Print) */
        .preview-toolbar {
            background: #0b284e;
            color: #fff;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 4px 14px rgba(11, 40, 78, 0.25);
        }
        .preview-toolbar .btn-action {
            border: none;
            padding: 7px 18px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            font-size: 13.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-print {
            background: #696cff;
            color: #fff;
        }
        .btn-print:hover {
            background: #5f61e6;
        }
        .btn-close-window {
            background: #334155;
            color: #cbd5e1;
        }
        .btn-close-window:hover {
            background: #475569;
            color: #fff;
        }
        .template-switcher-select {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-family: inherit;
            outline: none;
            cursor: pointer;
        }
        .template-switcher-select option {
            background: #0b284e;
            color: #fff;
        }

        /* Printable Paper Canvas */
        .page-sheet {
            background: #ffffff;
            width: {{ $orient === 'landscape' ? '297mm' : '210mm' }};
            min-height: {{ $orient === 'landscape' ? '210mm' : '297mm' }};
            padding: {{ $margins['top'] }}{{ $unit }} {{ $margins['right'] }}{{ $unit }} {{ $margins['bottom'] }}{{ $unit }} {{ $margins['left'] }}{{ $unit }};
            margin: 20px auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Header Section */
        .header-section {
            border-bottom: 2px solid #0b284e;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .header-grid {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .company-title {
            margin: 0 0 4px 0;
            font-size: 17px;
            font-weight: 800;
            color: #0b284e;
        }
        .company-meta {
            font-size: 11px;
            color: #64748b;
            line-height: 1.6;
        }
        .doc-title-box {
            text-align: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #cbd5e1;
        }
        .doc-title {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
        }
        .doc-meta {
            font-size: 11px;
            color: #64748b;
            margin-top: 4px;
        }

        /* Report Table Styling */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .report-table th {
            background-color: #f1f5f9 !important;
            color: #0b284e;
            font-weight: 800;
            font-size: 11px;
            padding: 7px 8px;
            border: 1px solid #cbd5e1;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        .report-table td {
            font-size: 10.5px;
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            vertical-align: middle;
        }
        .report-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .report-table th.text-center, .report-table td.text-center {
            text-align: center !important;
        }
        .report-table th.text-end, .report-table td.text-end {
            text-align: end !important;
        }

        /* Status Pills inside print */
        .badge-print {
            display: inline-block;
            padding: 2px 7px;
            font-size: 10px;
            font-weight: 700;
            border-radius: 4px;
            border: 1px solid transparent;
        }
        .badge-print-success { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
        .badge-print-danger { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }
        .badge-print-warning { background: #fef3c7; color: #b45309; border-color: #fde68a; }
        .badge-print-info { background: #e0f2fe; color: #0369a1; border-color: #bae6fd; }
        .badge-print-secondary { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }

        /* Signatures Grid */
        .signatures-grid {
            display: flex;
            gap: 20px;
            justify-content: space-around;
            margin-top: 28px;
            padding-top: 14px;
        }
        .signature-item {
            text-align: center;
            flex: 1;
            border-top: 1px dashed #94a3b8;
            padding-top: 6px;
        }
        .signature-title {
            font-weight: 700;
            font-size: 11.5px;
            margin-bottom: 30px;
            color: #334155;
        }
        .signature-name {
            font-size: 10.5px;
            color: #64748b;
        }

        /* Footer Section */
        .footer-section {
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            margin-top: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 10.5px;
            color: #64748b;
        }

        /* Custom Injected CSS */
        {!! $template->custom_css ?? '' !!}

        @media print {
            body {
                background: #ffffff !important;
            }
            .preview-toolbar {
                display: none !important;
            }
            .page-sheet {
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Floating Top Bar for Print Preview -->
    <div class="preview-toolbar">
        <div style="display: flex; align-items: center; gap: 14px;">
            <i class="bx bx-printer" style="font-size: 22px; color: #818cf8;"></i>
            <span style="font-weight: 800; font-size: 15px;">@yield('title', $template->display_name ?? __('Print Preview'))</span>
            
            @if(isset($availableTemplates) && $availableTemplates->count() > 1)
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 12px; opacity: 0.85;">{{ __('القالب:') }}</span>
                    <select class="template-switcher-select" onchange="window.location.href = this.value">
                        @foreach($availableTemplates as $t)
                            <option value="{{ request()->fullUrlWithQuery(['template_id' => $t->id]) }}" {{ ($template->id ?? null) == $t->id ? 'selected' : '' }}>
                                {{ $t->display_name }} ({{ $t->page_size }} - {{ ucfirst($t->orientation) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <span style="background: rgba(255,255,255,0.15); padding: 2px 10px; border-radius: 4px; font-size: 12px;">
                    {{ $size }} - {{ ucfirst($orient) }}
                </span>
            @endif
        </div>

        <div style="display: flex; gap: 10px;">
            <button class="btn-action btn-print" onclick="window.print()">
                <i class="bx bx-printer"></i>
                <span>{{ __('طباعة المستند') }}</span>
            </button>
            <button class="btn-action btn-close-window" onclick="window.close()">
                <i class="bx bx-x"></i>
                <span>{{ __('إغلاق') }}</span>
            </button>
        </div>
    </div>

    <!-- Printable Paper Sheet -->
    <div class="page-sheet">
        <div>
            <!-- Header Section -->
            @if(!empty($hdr['show_header']))
                <div class="header-section">
                    <div class="header-grid" style="flex-direction: {{ ($hdr['logo_position'] ?? 'right') === 'left' ? 'row-reverse' : 'row' }};">
                        <div>
                            @if(!empty($hdr['show_company_name']))
                                <h3 class="company-title">
                                    {{ $globalSetting->site_name ?? 'شركة جيت تك للتطوير التقني' }}
                                </h3>
                            @endif
                            <div class="company-meta">
                                @if(!empty($hdr['show_cr']) && !empty($globalSetting->cr_number ?? '1010987654'))
                                    <span>{{ __('س.ت:') }} {{ $globalSetting->cr_number ?? '1010987654' }}</span>
                                @endif
                                @if(!empty($hdr['show_tax']) && !empty($globalSetting->tax_number ?? '300123456700003'))
                                    <span style="margin: 0 6px;">|</span>
                                    <span>{{ __('الرقم الضريبي:') }} {{ $globalSetting->tax_number ?? '300123456700003' }}</span>
                                @endif
                                @if(!empty($hdr['show_phone']) && !empty($globalSetting->phone))
                                    <br><span>{{ __('الهاتف:') }} {{ $globalSetting->phone }}</span>
                                @endif
                                @if(!empty($hdr['show_email']) && !empty($globalSetting->email))
                                    <span style="margin: 0 6px;">|</span>
                                    <span>{{ $globalSetting->email }}</span>
                                @endif
                            </div>
                        </div>

                        @if(!empty($hdr['show_logo']))
                            <div>
                                @if(!empty($globalSetting->logo) && file_exists(public_path('storage/' . $globalSetting->logo)))
                                    <img src="{{ asset('storage/' . $globalSetting->logo) }}" style="max-height: 58px;" alt="Logo">
                                @else
                                    <img src="{{ asset('assets/img/branding/logo-dark.png') }}" style="max-height: 58px;" alt="Logo" onerror="this.outerHTML='<div style=\'font-weight:900;font-size:18px;color:#0b284e;\'>' + '{{ $globalSetting->site_name ?? 'GATE TECH' }}' + '</div>'">
                                @endif
                            </div>
                        @endif
                    </div>

                    @if(!empty($hdr['show_report_title']))
                        <div class="doc-title-box">
                            <h4 class="doc-title">@yield('report_title', $template->display_name)</h4>
                            <div class="doc-meta">
                                @yield('report_meta')
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Body Content / Table -->
            <div class="report-content-body">
                @yield('content')
            </div>
        </div>

        <div>
            <!-- Signatures Section -->
            @if(!empty($signatures))
                <div class="signatures-grid">
                    @foreach($signatures as $sig)
                        <div class="signature-item">
                            <div class="signature-title">{{ app()->getLocale() === 'ar' ? ($sig['title'] ?? '') : ($sig['title_en'] ?? $sig['title'] ?? '') }}</div>
                            <div class="signature-name">{{ ($sig['name'] ?? null) ?: '........................' }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Footer Section -->
            @if(!empty($ftr['show_footer']))
                <div class="footer-section">
                    <div>
                        @if(!empty($ftr['show_printed_by']))
                            <span>{{ __('طبع بواسطة:') }} {{ auth()->user()?->name ?? 'المستخدم' }}</span>
                        @endif
                        @if(!empty($ftr['show_timestamp']))
                            <span style="margin: 0 6px;">|</span>
                            <span>{{ now()->format('Y-m-d H:i') }}</span>
                        @endif
                    </div>

                    <div style="font-size: 10px; font-weight: 600;">
                        {{ $ftr['footer_text'] ?? '' }}
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        @if(!empty($ftr['show_page_number']))
                            <span>{{ __('صفحة 1 من 1') }}</span>
                        @endif
                        @if(!empty($ftr['show_qr_code']))
                            <i class="bx bx-qr" style="font-size: 18px;"></i>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    @stack('scripts')
</body>
</html>
