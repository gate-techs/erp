<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>{{ __('No Internet Connection') }} | {{ __('ALA ORBIT') }}</title>
    <!-- Google Fonts (Almarai) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/{{ app()->getLocale() === 'ar' ? 'bootstrap.rtl.min.css' : 'bootstrap.min.css' }}" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/portal/css/portal.css') }}?v={{ file_exists(public_path('assets/portal/css/portal.css')) ? filemtime(public_path('assets/portal/css/portal.css')) : time() }}" />
</head>

<body class="portal-offline-body">
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
