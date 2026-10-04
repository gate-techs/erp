<!DOCTYPE html>
<html lang="{{ $locale ?? 'ar' }}" dir="{{ ($locale ?? 'ar') === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('app.name', 'ERP') }}</title>
    <style>
        /* Responsive Reset */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background-color: #f4f6f8;
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            direction: {{ ($locale ?? 'ar') === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ ($locale ?? 'ar') === 'ar' ? 'right' : 'left' }};
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f6f8;
            padding: 30px 10px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background-color: #0f2d59;
            padding: 24px 30px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .email-body {
            padding: 35px 30px;
            font-size: 15px;
            line-height: 1.7;
            color: #334155;
        }
        .email-body h2, .email-body h3 {
            color: #0f2d59;
            margin-top: 0;
        }
        .email-body p {
            margin-bottom: 16px;
        }
        .highlight-box {
            background-color: #f8fafc;
            border-{{ ($locale ?? 'ar') === 'ar' ? 'right' : 'left' }}: 4px solid #0f2d59;
            padding: 16px 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .btn-action {
            display: inline-block;
            background-color: #0f2d59;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 15px;
            text-align: center;
            margin: 24px 0 10px 0;
            box-shadow: 0 2px 6px rgba(15, 45, 89, 0.2);
        }
        .btn-action:hover {
            background-color: #1a427b;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #edf2f7;
        }
        .email-footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <h1>{{ config('app.name', 'ERP System') }}</h1>
            </div>

            <!-- Body -->
            <div class="email-body">
                {!! $bodyContent !!}

                @if (!empty($actionButton) && !empty($actionButton['url']) && !empty($actionButton['text']))
                    <div style="text-align: center; margin-top: 25px;">
                        <a href="{{ $actionButton['url'] }}" class="btn-action" target="_blank">
                            {{ $actionButton['text'] }}
                        </a>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'ERP System') }}. {{ ($locale ?? 'ar') === 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</p>
                <p style="font-size: 11px;">{{ ($locale ?? 'ar') === 'ar' ? 'تم إرسال هذا البريد الإلكتروني تلقائياً من نظام الإدارة الإلكتروني.' : 'This is an automated email from the enterprise management system.' }}</p>
            </div>
        </div>
    </div>
</body>
</html>
