<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Employee Onboarding - Complete Your Profile') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f5f9;
            color: #384551;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e7eaf0;
        }
        .header {
            background: linear-gradient(135deg, #696cff 0%, #4a4dec 100%);
            color: #ffffff;
            padding: 35px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header p {
            margin: 8px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 35px 30px;
            line-height: 1.6;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #2b3445;
        }
        .badge {
            display: inline-block;
            background: #e7e7ff;
            color: #696cff;
            padding: 4px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
        }
        .btn-container {
            text-align: center;
            margin: 35px 0;
        }
        .btn {
            background-color: #696cff;
            color: #ffffff !important;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.35);
        }
        .link-fallback {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            font-size: 12px;
            word-break: break-all;
            color: #6c757d;
            border: 1px dashed #d9dee3;
        }
        .footer {
            background-color: #fafafc;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #8592a3;
            border-top: 1px solid #ebeef2;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ config('app.name', 'ERP System') }}</h1>
            <p>{{ __('New Employee Onboarding') }}</p>
        </div>
        <div class="content">
            <div class="greeting">
                {{ __('Hello') }} {{ $employeeName }},
            </div>
            <p>
                {{ __('Welcome to our team! To complete your employment profile in our system, please follow the link below to verify and fill in your official details, banking information, and required identity documents.') }}
            </p>
            @if(!empty($employeeCode))
                <p>
                    <strong>{{ __('Employee Code:') }}</strong> <span class="badge">{{ $employeeCode }}</span>
                </p>
            @endif

            <div class="btn-container">
                <a href="{{ $onboardingUrl }}" class="btn" target="_blank">
                    {{ __('Complete Your Employee Profile') }} →
                </a>
            </div>

            <p style="font-size: 13px; color: #697a8d;">
                {{ __('If the button above does not work, copy and paste the following link into your browser:') }}
            </p>
            <div class="link-fallback">
                {{ $onboardingUrl }}
            </div>
        </div>
        <div class="footer">
            <p style="margin: 0;">{{ __('This link is uniquely generated for your employee record. Please do not share it.') }}</p>
            <p style="margin: 4px 0 0;">&copy; {{ date('Y') }} {{ config('app.name', 'ERP System') }}. {{ __('All rights reserved.') }}</p>
        </div>
    </div>
</body>
</html>

