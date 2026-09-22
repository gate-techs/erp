@extends('layouts/contentNavbarLayout')

@section('title', __('System Settings') . ' - ' . config('variables.templateName'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="mb-4">
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-cog text-primary"></i>
            {{ __('System Settings') }}
        </h4>
        <p class="text-muted mb-0">{{ __('Settings Management') }}</p>
    </div>

    @if (isset($errors) && $errors->any())
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

    <!-- Main Full Width Settings Form -->
    <form id="settingsForm" action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Section 1: Visual Identity & Brand Color -->
        <div class="card shadow-none border mb-4">
            <div class="card-header border-bottom py-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-palette text-primary font-20"></i>
                    {{ __('Visual Identity & Logos') }}
                </h5>
                <small class="text-muted">{{ __('Upload academy logo and favorite icon') }}</small>
            </div>
            <div class="card-body pt-4">
                <div class="row g-4">
                    <!-- Logo -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold mb-2">{{ __('Academy Logo') }}</label>
                        <div class="d-flex align-items-center gap-3 p-3 border rounded bg-lighter">
                            <div class="bg-white border rounded p-2 d-flex align-items-center justify-content-center" style="width: 170px; height: 80px; flex-shrink: 0;">
                                <img id="logoPreview" src="{{ $settings->logo_url }}" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;" />
                            </div>
                            <div>
                                <label for="logoInput" class="btn btn-sm btn-outline-primary mb-2">
                                    <i class="icon-base bx bx-upload me-1"></i>
                                    {{ __('Click to upload image') }}
                                    <input type="file" id="logoInput" name="logo" class="d-none" accept="image/*" onchange="previewImage(this, 'logoPreview')">
                                </label>
                                <div class="text-muted small">PNG, JPG, SVG (Max 2MB)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Favicon -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold mb-2">{{ __('Website Favicon') }}</label>
                        <div class="d-flex align-items-center gap-3 p-3 border rounded bg-lighter">
                            <div class="bg-white border rounded p-2 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; flex-shrink: 0;">
                                <img id="faviconPreview" src="{{ $settings->favicon_url }}" alt="Favicon" style="max-width: 40px; max-height: 40px; object-fit: contain;" />
                            </div>
                            <div>
                                <label for="faviconInput" class="btn btn-sm btn-outline-primary mb-2">
                                    <i class="icon-base bx bx-upload me-1"></i>
                                    {{ __('Click to upload image') }}
                                    <input type="file" id="faviconInput" name="favicon" class="d-none" accept="image/*" onchange="previewImage(this, 'faviconPreview')">
                                </label>
                                <div class="text-muted small">ICO, PNG (32x32 px)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Primary Color Picker -->
                    <div class="col-12">
                        <label class="form-label fw-semibold mb-2" for="primaryColorText">{{ __('Primary Brand Color') }}</label>
                        <div class="p-3 border rounded bg-lighter d-flex flex-wrap align-items-center gap-3">
                            <input type="color" class="form-control form-control-color border rounded" id="primaryColorPicker" value="{{ old('primary_color', $settings->primary_color ?? '#0b284e') }}" style="width: 50px; height: 40px; cursor: pointer;" title="Choose Color" onchange="document.getElementById('primaryColorText').value = this.value">
                            <input type="text" class="form-control" name="primary_color" id="primaryColorText" value="{{ old('primary_color', $settings->primary_color ?? '#0b284e') }}" style="max-width: 140px;" dir="ltr" oninput="document.getElementById('primaryColorPicker').value = this.value">
                            <span class="text-muted small">{{ __('Unified primary color for buttons and UI elements') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Translatable Information (Side-by-Side Arabic & English) -->
        <div class="card shadow-none border mb-4">
            <div class="card-header border-bottom py-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-globe text-primary font-20"></i>
                    {{ __('Translatable Academy Information') }}
                </h5>
                <small class="text-muted">{{ __('Name, slogan and address in Arabic and English') }}</small>
            </div>
            <div class="card-body pt-4">
                <div class="row g-4">
                    <!-- Arabic Content -->
                    <div class="col-12 col-lg-6">
                        <div class="border rounded p-3 h-100 bg-lighter">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="fw-bold text-dark font-15 d-flex align-items-center gap-1">
                                    <i class="icon-base bx bx-font text-primary"></i>
                                    {{ __('Arabic Content') }}
                                </span>
                                <span class="badge bg-label-primary">العربية (AR)</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">{{ __('Academy Name') }}</label>
                                    <input type="text" name="ar[site_name]" class="form-control" value="{{ old('ar.site_name', $settings->translate('ar')?->site_name) }}" placeholder="أكاديمية أسترو ليدرز">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('Slogan') }}</label>
                                    <input type="text" name="ar[slogan]" class="form-control" value="{{ old('ar.slogan', $settings->translate('ar')?->slogan) }}" placeholder="نصنع قادة الغد">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('Headquarters Address') }}</label>
                                    <input type="text" name="ar[address]" class="form-control" value="{{ old('ar.address', $settings->translate('ar')?->address) }}" placeholder="الكويت">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('Footer Copyright Text') }}</label>
                                    <input type="text" name="ar[footer_text]" class="form-control" value="{{ old('ar.footer_text', $settings->translate('ar')?->footer_text) }}" placeholder="جميع الحقوق محفوظة">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- English Content -->
                    <div class="col-12 col-lg-6">
                        <div class="border rounded p-3 h-100 bg-lighter">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="fw-bold text-dark font-15 d-flex align-items-center gap-1">
                                    <i class="icon-base bx bx-font text-info"></i>
                                    {{ __('English Content') }}
                                </span>
                                <span class="badge bg-label-info">English (EN)</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">{{ __('Academy Name') }}</label>
                                    <input type="text" name="en[site_name]" class="form-control" value="{{ old('en.site_name', $settings->translate('en')?->site_name) }}" placeholder="Astro Leaders Academy" dir="ltr">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('Slogan') }}</label>
                                    <input type="text" name="en[slogan]" class="form-control" value="{{ old('en.slogan', $settings->translate('en')?->slogan) }}" placeholder="Shaping Tomorrow's Leaders" dir="ltr">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('Headquarters Address') }}</label>
                                    <input type="text" name="en[address]" class="form-control" value="{{ old('en.address', $settings->translate('en')?->address) }}" placeholder="Kuwait" dir="ltr">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('Footer Copyright Text') }}</label>
                                    <input type="text" name="en[footer_text]" class="form-control" value="{{ old('en.footer_text', $settings->translate('en')?->footer_text) }}" placeholder="All rights reserved" dir="ltr">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Official Contact Information -->
        <div class="card shadow-none border mb-4">
            <div class="card-header border-bottom py-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-phone-call text-primary font-20"></i>
                    {{ __('Official Contact Information') }}
                </h5>
                <small class="text-muted">{{ __('Official phone numbers and emergency contacts') }}</small>
            </div>
            <div class="card-body pt-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label">{{ __('Official Email') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="icon-base bx bx-envelope"></i></span>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $settings->email) }}" placeholder="info@astroleaderskw.com" dir="ltr">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label">{{ __('Primary Phone') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="icon-base bx bx-phone"></i></span>
                            <input type="text" name="phone" class="form-control js-phone-input" value="{{ old('phone', $settings->phone) }}" placeholder="+965 xxxxxxxx" dir="ltr">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label">{{ __('WhatsApp Number') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="icon-base bx bxl-whatsapp text-success"></i></span>
                            <input type="text" name="whatsapp" class="form-control js-phone-input" value="{{ old('whatsapp', $settings->whatsapp) }}" placeholder="+965 xxxxxxxx" dir="ltr">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label text-danger">{{ __('Emergency Phone') }}</label>
                        <div class="input-group">
                            <span class="input-group-text text-danger"><i class="icon-base bx bx-alarm-exclamation"></i></span>
                            <input type="text" name="emergency_phone" class="form-control js-phone-input" value="{{ old('emergency_phone', $settings->emergency_phone) }}" placeholder="+965 xxxxxxxx" dir="ltr">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Regional & Financial Settings -->
        <div class="card shadow-none border mb-4">
            <div class="card-header border-bottom py-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-coin-stack text-primary font-20"></i>
                    {{ __('Regional & Financial Settings') }}
                </h5>
                <small class="text-muted">{{ __('Currencies, timezones and tax registration') }}</small>
            </div>
            <div class="card-body pt-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label">{{ __('Currency Code') }}</label>
                        <input type="text" name="currency" class="form-control" value="{{ old('currency', $settings->currency) }}" placeholder="KWD" dir="ltr">
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label">{{ __('Currency Symbol') }}</label>
                        <input type="text" name="currency_symbol" class="form-control" value="{{ old('currency_symbol', $settings->currency_symbol) }}" placeholder="د.ك">
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label">{{ __('System Timezone') }}</label>
                        <select name="timezone" class="form-select" dir="ltr">
                            <option value="Asia/Kuwait" {{ old('timezone', $settings->timezone) === 'Asia/Kuwait' ? 'selected' : '' }}>Asia/Kuwait (GMT+3)</option>
                            <option value="Asia/Riyadh" {{ old('timezone', $settings->timezone) === 'Asia/Riyadh' ? 'selected' : '' }}>Asia/Riyadh (GMT+3)</option>
                            <option value="Asia/Dubai" {{ old('timezone', $settings->timezone) === 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GMT+4)</option>
                            <option value="Africa/Cairo" {{ old('timezone', $settings->timezone) === 'Africa/Cairo' ? 'selected' : '' }}>Africa/Cairo (GMT+2)</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label">{{ __('Default System Language') }}</label>
                        <select name="default_locale" class="form-select">
                            <option value="ar" {{ old('default_locale', $settings->default_locale) === 'ar' ? 'selected' : '' }}>العربية (Arabic)</option>
                            <option value="en" {{ old('default_locale', $settings->default_locale) === 'en' ? 'selected' : '' }}>English</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('Tax / Commercial Register Number') }}</label>
                        <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number', $settings->tax_number) }}" placeholder="Commercial / Tax ID">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Social Media Accounts -->
        <div class="card shadow-none border mb-4">
            <div class="card-header border-bottom py-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-share-alt text-primary font-20"></i>
                    {{ __('Social Media Accounts') }}
                </h5>
                <small class="text-muted">{{ __('Official academy social media links') }}</small>
            </div>
            <div class="card-body pt-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Instagram</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="icon-base bx bxl-instagram text-danger"></i></span>
                            <input type="text" name="instagram" class="form-control" value="{{ old('instagram', $settings->instagram) }}" placeholder="https://instagram.com/username" dir="ltr">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Facebook</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="icon-base bx bxl-facebook text-primary"></i></span>
                            <input type="text" name="facebook" class="form-control" value="{{ old('facebook', $settings->facebook) }}" placeholder="https://facebook.com/pagename" dir="ltr">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">X (Twitter)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="icon-base bx bxl-twitter text-info"></i></span>
                            <input type="text" name="twitter" class="form-control" value="{{ old('twitter', $settings->twitter) }}" placeholder="https://twitter.com/username" dir="ltr">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">TikTok</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="icon-base bx bxl-tiktok text-dark"></i></span>
                            <input type="text" name="tiktok" class="form-control" value="{{ old('tiktok', $settings->tiktok) }}" placeholder="https://tiktok.com/@username" dir="ltr">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">YouTube</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="icon-base bx bxl-youtube text-danger"></i></span>
                            <input type="text" name="youtube" class="form-control" value="{{ old('youtube', $settings->youtube) }}" placeholder="https://youtube.com/@channel" dir="ltr">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky / Bottom Actions Card -->
        <div class="card shadow-none border bg-lighter p-3 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <span class="text-muted small">
                    <i class="icon-base bx bx-info-circle me-1 text-primary"></i>
                    {{ __('Make sure to review all data before saving, changes affect all UI interfaces.') }}
                </span>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 px-4 shadow-sm">
                    <i class="icon-base bx bx-save font-18"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
