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

            <!-- Primary Color -->
            <label class="customizer-option-label mb-2 d-block mt-4">{{ __('Primary Color') }}</label>
            <div class="d-flex flex-wrap gap-2" id="customizerPrimaryColors">
                <div class="customizer-primary-swatch active" data-color="#0b284e" style="background:#0b284e;" title="Default"></div>
                <div class="customizer-primary-swatch" data-color="#696cff" style="background:#696cff;" title="Purple"></div>
                <div class="customizer-primary-swatch" data-color="#03c3ec" style="background:#03c3ec;" title="Info"></div>
                <div class="customizer-primary-swatch" data-color="#71dd37" style="background:#71dd37;" title="Success"></div>
                <div class="customizer-primary-swatch" data-color="#ff3e1d" style="background:#ff3e1d;" title="Danger"></div>
                <div class="customizer-primary-swatch" data-color="#ffab00" style="background:#ffab00;" title="Warning"></div>
                <div class="customizer-primary-swatch" data-color="#e83e8c" style="background:#e83e8c;" title="Pink"></div>
                <div class="customizer-primary-swatch customizer-primary-custom" title="{{ __('Custom Color') }}" style="background: conic-gradient(red, yellow, lime, aqua, blue, magenta, red);">
                    <input type="color" id="customizerCustomColor" title="{{ __('Pick a color') }}">
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
