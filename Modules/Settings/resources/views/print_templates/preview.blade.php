<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $template->display_name }} - {{ __('Print Preview') }}</title>
    
    <!-- Google Fonts Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons / Boxicons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    @php
        $margins = $template->margins ?? ['top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12, 'unit' => 'mm'];
        $unit = $margins['unit'] ?? 'mm';
        $orient = $template->orientation ?: 'portrait';
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
            background: #e2e8f0;
            color: #1e293b;
            font-size: 13px;
        }
        .preview-toolbar {
            background: #1e293b;
            color: #fff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .print-btn {
            background: #696cff;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .print-btn:hover {
            background: #5f61e6;
        }
        .page-sheet {
            background: #ffffff;
            width: {{ $orient === 'landscape' ? '297mm' : '210mm' }};
            min-height: {{ $orient === 'landscape' ? '210mm' : '297mm' }};
            padding: {{ $margins['top'] }}{{ $unit }} {{ $margins['right'] }}{{ $unit }} {{ $margins['bottom'] }}{{ $unit }} {{ $margins['left'] }}{{ $unit }};
            margin: 24px auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .header-section {
            border-bottom: 2px solid #1e293b;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-grid {
            display: flex;
            align-items: center;
            justify-content: space-between;
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
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .report-table th {
            background-color: #f8fafc !important;
            color: #0f172a;
            font-weight: 700;
            font-size: 11.5px;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        .report-table td {
            font-size: 11.5px;
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        .report-table tr:nth-child(even) td {
            background-color: #fafbfc;
        }
        .signatures-grid {
            display: flex;
            gap: 20px;
            justify-content: space-around;
            margin-top: 30px;
            padding-top: 15px;
        }
        .signature-item {
            text-align: center;
            flex: 1;
            border-top: 1px dashed #94a3b8;
            padding-top: 6px;
        }
        .signature-title {
            font-weight: 700;
            font-size: 12px;
            margin-bottom: 35px;
        }
        .signature-name {
            font-size: 11px;
            color: #64748b;
        }
        .footer-section {
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            color: #64748b;
        }

        /* Custom User CSS */
        {!! $template->custom_css !!}

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
</head>
<body>

    <!-- Floating Top Bar (No Print) -->
    <div class="preview-toolbar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <i class="bx bx-printer" style="font-size: 22px; color: #696cff;"></i>
            <span style="font-weight: 700; font-size: 15px;">{{ $template->display_name }} ({{ __('Print Preview') }})</span>
            <span style="background: rgba(255,255,255,0.15); padding: 2px 10px; border-radius: 4px; font-size: 12px;">
                {{ $size }} - {{ ucfirst($orient) }}
            </span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button class="print-btn" onclick="window.print()">
                <i class="bx bx-printer"></i>
                <span>{{ __('Print Document') }}</span>
            </button>
            <button class="print-btn" style="background: #475569;" onclick="window.close()">
                <i class="bx bx-x"></i>
                <span>{{ __('Close') }}</span>
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
                                <h3 style="margin: 0 0 4px 0; font-size: 18px; font-weight: 800; color: #0f172a;">
                                    {{ $globalSetting->site_name ?? 'شركة جيت تك للتطوير التقني' }}
                                </h3>
                            @endif
                            <div style="font-size: 11.5px; color: #64748b; line-height: 1.6;">
                                @if(!empty($hdr['show_cr']))
                                    <span>س.ت: 1010987654</span>
                                @endif
                                @if(!empty($hdr['show_tax']))
                                    <span style="margin: 0 6px;">|</span>
                                    <span>الرقم الضريبي: 300123456700003</span>
                                @endif
                                @if(!empty($hdr['show_phone']))
                                    <br><span>الهاتف: +966 11 234 5678</span>
                                @endif
                                @if(!empty($hdr['show_email']))
                                    <span style="margin: 0 6px;">|</span>
                                    <span>info@gatetech.com</span>
                                @endif
                            </div>
                        </div>

                        @if(!empty($hdr['show_logo']))
                            <div>
                                <img src="{{ asset('assets/img/branding/logo-dark.png') }}" style="max-height: 60px;" alt="Logo" onerror="this.outerHTML='<div style=\'font-weight:800;font-size:16px;color:#696cff;\'>[LOGO]</div>'">
                            </div>
                        @endif
                    </div>

                    @if(!empty($hdr['show_report_title']))
                        <div class="doc-title-box">
                            <h4 class="doc-title">{{ $template->display_name }}</h4>
                            <div class="doc-meta">
                                <span>تاريخ الاستخراج: {{ now()->format('Y/m/d') }}</span>
                                <span style="margin: 0 8px;">•</span>
                                <span>نطاق البيانات: كافة الفروع والأقسام</span>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Body Content (Sample Data) -->
            <div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center;">#</th>
                            <th style="width: 100px;">الرقم الوظيفي</th>
                            <th>اسم الموظف</th>
                            <th>المسمى الوظيفي</th>
                            <th>القسم / الإدارة</th>
                            <th>الفرع</th>
                            <th style="width: 80px; text-align: center;">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center;">1</td>
                            <td>EMP-1001</td>
                            <td><strong>منى العتيبي</strong></td>
                            <td>أخصائي موارد بشرية أول</td>
                            <td>إدارة الموارد البشرية</td>
                            <td>المركز الرئيسي - الرياض</td>
                            <td style="text-align: center;"><span style="color: #16a34a; font-weight: bold;">نشط</span></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">2</td>
                            <td>EMP-1002</td>
                            <td><strong>أحمد المحمدي</strong></td>
                            <td>محاسب مالي معتمد</td>
                            <td>الإدارة المالية</td>
                            <td>فرع جدة</td>
                            <td style="text-align: center;"><span style="color: #16a34a; font-weight: bold;">نشط</span></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">3</td>
                            <td>EMP-1003</td>
                            <td><strong>سارة القحطاني</strong></td>
                            <td>مشرفة رياض أطفال</td>
                            <td>قطاع الحضانات</td>
                            <td>فرع الروضة</td>
                            <td style="text-align: center;"><span style="color: #16a34a; font-weight: bold;">نشط</span></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">4</td>
                            <td>EMP-1004</td>
                            <td><strong>خالد العلي</strong></td>
                            <td>مهندس برمجيات ونظم</td>
                            <td>تقنية المعلومات</td>
                            <td>المركز الرئيسي - الرياض</td>
                            <td style="text-align: center;"><span style="color: #16a34a; font-weight: bold;">نشط</span></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">5</td>
                            <td>EMP-1005</td>
                            <td><strong>ريم الشهري</strong></td>
                            <td>مسؤول علاقات حكومية</td>
                            <td>الشؤون الإدارية</td>
                            <td>فرع الدمام</td>
                            <td style="text-align: center;"><span style="color: #16a34a; font-weight: bold;">نشط</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <!-- Signatures Section -->
            @if(!empty($signatures))
                <div class="signatures-grid">
                    @foreach($signatures as $sig)
                        <div class="signature-item">
                            <div class="signature-title">{{ app()->getLocale() === 'ar' ? ($sig['title'] ?? '') : ($sig['title_en'] ?? $sig['title'] ?? '') }}</div>
                            <div class="signature-name">{{ ($sig['name'] ?? null) ?: '(التوقيع والاعتماد)' }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Footer Section -->
            @if(!empty($ftr['show_footer']))
                <div class="footer-section">
                    <div>
                        @if(!empty($ftr['show_printed_by']))
                            <span>طبع بواسطة: {{ auth()->user()?->name ?? 'User' }}</span>
                        @endif
                        @if(!empty($ftr['show_timestamp']))
                            <span style="margin: 0 6px;">|</span>
                            <span>{{ now()->format('Y-m-d H:i') }}</span>
                        @endif
                    </div>

                    <div style="font-size: 10.5px; font-weight: 600;">
                        {{ $ftr['footer_text'] ?? '' }}
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        @if(!empty($ftr['show_page_number']))
                            <span>صفحة 1 من 1</span>
                        @endif
                        @if(!empty($ftr['show_qr_code']))
                            <i class="bx bx-qr" style="font-size: 20px;"></i>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

</body>
</html>
