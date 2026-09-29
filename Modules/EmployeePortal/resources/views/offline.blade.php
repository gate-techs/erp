<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>{{ __('No Internet Connection') }} | {{ __('Astro Leaders') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/{{ app()->getLocale() === 'ar' ? 'bootstrap.rtl.min.css' : 'bootstrap.min.css' }}" rel="stylesheet" />

    <style>
        body {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
            background-color: #00334B;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
            text-align: center;
        }

        .offline-card {
            background: #ffffff;
            color: #1E293B;
            border-radius: 24px;
            padding: 40px 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 380px;
            width: 100%;
        }

        .offline-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            margin: 0 auto 20px auto;
        }

        .btn-retry {
            background: #00334B;
            color: #ffffff;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 700;
            width: 100%;
            border: none;
        }
    </style>
</head>

<body>
    <div class="offline-card">
        <div class="offline-icon">
            <i class="bx bx-wifi-off"></i>
        </div>

        <h4 class="fw-bold mb-2">{{ __('No Internet Connection') }}</h4>
        <p class="text-muted fs-7 mb-4">
            {{ __('Please check your internet connection and try again.') }}
        </p>

        <button type="button" class="btn-retry" onclick="window.location.reload()">
            <i class="bx bx-refresh me-1"></i> {{ __('Try Again') }}
        </button>
    </div>
</body>

</html>
