@php
    $customizerDir = str_starts_with(strtolower(app()->getLocale()), 'ar') ? 'rtl' : 'ltr';
@endphp
<div class="theme-customizer-backdrop" id="themeCustomizerBackdrop"></div>

<div class="theme-customizer" id="themeCustomizer" dir="{{ $customizerDir }}">
    <div class="theme-customizer-toggle" id="themeCustomizerToggle">
        <i class="bx bx-cog bx-spin"></i>
    </div>
    <div class="theme-customizer-header">
        <h5>{{ __('Theme Customizer') }}</h5>
        <button class="theme-customizer-close" id="themeCustomizerClose">&times;</button>
    </div>
    
    <div class="theme-customizer-body">
        
        <!-- Theming -->
        <div class="customizer-section">
            <span class="customizer-section-title">{{ __('Theming') }}</span>
            
            <!-- Skin -->
            <label class="customizer-option-label mb-2 d-block">{{ __('Skin (Mode)') }}</label>
            <div class="customizer-radio-group">
                <div class="customizer-radio-item">
                    <input type="radio" name="customizer_skin" id="skin_light" value="light" checked>
                    <label for="skin_light">
                        <i class="bx bx-sun fs-3"></i>
                        {{ __('Light') }}
                    </label>
                </div>
                <div class="customizer-radio-item">
                    <input type="radio" name="customizer_skin" id="skin_dark" value="dark">
                    <label for="skin_dark">
                        <i class="bx bx-moon fs-3"></i>
                        {{ __('Dark') }}
                    </label>
                </div>
            </div>
        </div>
        
        <hr class="my-4">
        
        <!-- Layout -->
        <div class="customizer-section">
            <span class="customizer-section-title">{{ __('Layout') }}</span>
            
            <!-- Content Width -->
            <label class="customizer-option-label mb-2 d-block">{{ __('Content Width') }}</label>
            <div class="customizer-radio-group">
                <div class="customizer-radio-item">
                    <input type="radio" name="customizer_content_width" id="width_boxed" value="boxed" checked>
                    <label for="width_boxed">
                        <i class="bx bx-collapse fs-3"></i>
                        {{ __('Boxed') }}
                    </label>
                </div>
                <div class="customizer-radio-item">
                    <input type="radio" name="customizer_content_width" id="width_full" value="full">
                    <label for="width_full">
                        <i class="bx bx-expand fs-3"></i>
                        {{ __('Full') }}
                    </label>
                </div>
            </div>

            <!-- Direction -->
            <div class="customizer-option-row mt-4">
                <label class="customizer-option-label" for="customizer_rtl">{{ __('RTL Direction (Current Locale)') }}</label>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="customizer_rtl">
                </div>
            </div>

            <!-- Menu Collapsed -->
            <div class="customizer-option-row">
                <label class="customizer-option-label" for="customizer_menu_collapsed">{{ __('Menu Collapsed') }}</label>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="customizer_menu_collapsed">
                </div>
            </div>
            
            <!-- Menu Hidden -->
            <div class="customizer-option-row">
                <label class="customizer-option-label" for="customizer_menu_hidden">{{ __('Menu Hidden') }}</label>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="customizer_menu_hidden">
                </div>
            </div>
        </div>
        
        <hr class="my-4">
        
        <!-- Navbar & Footer -->
        <div class="customizer-section">
            <span class="customizer-section-title">{{ __('Navbar & Footer') }}</span>

            <!-- Navbar Color -->
            <label class="customizer-option-label mb-2 d-block">{{ __('Navbar Color') }}</label>
            <div class="d-flex gap-2 mb-4 flex-wrap">
                <div class="customizer-color-item color-bg-theme">
                    <input type="radio" name="customizer_navbar_color" id="navcolor_theme" value="bg-navbar-theme" checked>
                    <label for="navcolor_theme"></label>
                </div>
                <div class="customizer-color-item color-bg-primary">
                    <input type="radio" name="customizer_navbar_color" id="navcolor_primary" value="bg-primary">
                    <label for="navcolor_primary"></label>
                </div>
                <div class="customizer-color-item color-bg-secondary">
                    <input type="radio" name="customizer_navbar_color" id="navcolor_secondary" value="bg-secondary">
                    <label for="navcolor_secondary"></label>
                </div>
                <div class="customizer-color-item color-bg-success">
                    <input type="radio" name="customizer_navbar_color" id="navcolor_success" value="bg-success">
                    <label for="navcolor_success"></label>
                </div>
                <div class="customizer-color-item color-bg-danger">
                    <input type="radio" name="customizer_navbar_color" id="navcolor_danger" value="bg-danger">
                    <label for="navcolor_danger"></label>
                </div>
                <div class="customizer-color-item color-bg-warning">
                    <input type="radio" name="customizer_navbar_color" id="navcolor_warning" value="bg-warning">
                    <label for="navcolor_warning"></label>
                </div>
                <div class="customizer-color-item color-bg-info">
                    <input type="radio" name="customizer_navbar_color" id="navcolor_info" value="bg-info">
                    <label for="navcolor_info"></label>
                </div>
                <div class="customizer-color-item color-bg-dark">
                    <input type="radio" name="customizer_navbar_color" id="navcolor_dark" value="bg-dark">
                    <label for="navcolor_dark"></label>
                </div>
            </div>
            
            <!-- Navbar Type -->
            <label class="customizer-option-label mb-2 d-block">{{ __('Navbar Type') }}</label>
            <div class="customizer-radio-group">
                <div class="customizer-radio-item">
                    <input type="radio" name="customizer_navbar_type" id="nav_sticky" value="sticky" checked>
                    <label for="nav_sticky">{{ __('Sticky') }}</label>
                </div>
                <div class="customizer-radio-item">
                    <input type="radio" name="customizer_navbar_type" id="nav_static" value="static">
                    <label for="nav_static">{{ __('Static') }}</label>
                </div>
                <div class="customizer-radio-item">
                    <input type="radio" name="customizer_navbar_type" id="nav_hidden" value="hidden">
                    <label for="nav_hidden">{{ __('Hidden') }}</label>
                </div>
            </div>

            <!-- Footer Type -->
            <label class="customizer-option-label mb-2 d-block mt-4">{{ __('Footer Type') }}</label>
            <div class="customizer-radio-group">
                <div class="customizer-radio-item">
                    <input type="radio" name="customizer_footer_type" id="footer_sticky" value="sticky">
                    <label for="footer_sticky">{{ __('Sticky') }}</label>
                </div>
                <div class="customizer-radio-item">
                    <input type="radio" name="customizer_footer_type" id="footer_static" value="static" checked>
                    <label for="footer_static">{{ __('Static') }}</label>
                </div>
                <div class="customizer-radio-item">
                    <input type="radio" name="customizer_footer_type" id="footer_hidden" value="hidden">
                    <label for="footer_hidden">{{ __('Hidden') }}</label>
                </div>
            </div>
        </div>

        <hr class="my-4">
        <div class="d-grid pb-3">
            <button class="btn btn-outline-danger d-flex align-items-center justify-content-center gap-2" id="themeCustomizerReset" data-confirm-msg="{{ __('Are you sure you want to reset all theme settings to defaults?') }}">
                <i class="bx bx-reset"></i> <span>{{ __('Reset to Defaults') }}</span>
            </button>
        </div>

    </div>
</div>
