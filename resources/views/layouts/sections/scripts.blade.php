<!-- BEGIN: Vendor JS-->

@vite(['resources/assets/vendor/libs/jquery/jquery.js', 'resources/assets/vendor/libs/popper/popper.js', 'resources/assets/vendor/js/bootstrap.js'])

@vite(['resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js', 'resources/assets/vendor/js/menu.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js'])

@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
@vite(['resources/assets/js/main.js'])

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
@yield('scripts')
@stack('scripts')
<!-- END: Page JS-->

<!-- app JS -->
@vite(['resources/js/app.js'])
<!-- END: app JS-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeBtn = document.getElementById('theme-toggle-btn');
    if (themeBtn) {
        const sunIcon = themeBtn.querySelector('.theme-icon-sun');
        const moonIcon = themeBtn.querySelector('.theme-icon-moon');
        const savedTheme = localStorage.getItem('astro_theme') || 'light';
        
        if (savedTheme === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
            if (sunIcon && moonIcon) {
                sunIcon.classList.add('d-none');
                moonIcon.classList.remove('d-none');
            }
        }
        
        themeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('astro_theme', newTheme);
            
            if (sunIcon && moonIcon) {
                if (newTheme === 'dark') {
                    sunIcon.classList.add('d-none');
                    moonIcon.classList.remove('d-none');
                } else {
                    sunIcon.classList.remove('d-none');
                    moonIcon.classList.add('d-none');
                }
            }
        });
    }

    // =====================================================
    // PRIMARY COLOR CUSTOMIZER
    // =====================================================
    /**
     * Convert a hex color (#rrggbb) to "r, g, b" string for --bs-*-rgb variables.
     */
    function hexToRgb(hex) {
        hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
        const bigint = parseInt(hex, 16);
        return [(bigint >> 16) & 255, (bigint >> 8) & 255, bigint & 255].join(', ');
    }

    /**
     * Darken/lighten a hex color by a percentage (negative = darker, positive = lighter).
     */
    function shadeColor(hex, percent) {
        hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
        let r = parseInt(hex.substring(0,2),16);
        let g = parseInt(hex.substring(2,4),16);
        let b = parseInt(hex.substring(4,6),16);
        r = Math.min(255, Math.max(0, Math.round(r * (1 + percent/100))));
        g = Math.min(255, Math.max(0, Math.round(g * (1 + percent/100))));
        b = Math.min(255, Math.max(0, Math.round(b * (1 + percent/100))));
        return '#' + [r,g,b].map(x => x.toString(16).padStart(2,'0')).join('');
    }

    /**
     * Apply a primary color to all Bootstrap + template CSS variables on :root.
     */
    function applyPrimaryColor(color) {
        const root = document.documentElement;
        const rgb  = hexToRgb(color);
        const hoverColor = shadeColor(color, -10);
        const activeColor = shadeColor(color, -15);

        // Core Bootstrap primary vars
        root.style.setProperty('--bs-primary',           color);
        root.style.setProperty('--bs-primary-rgb',       rgb);
        root.style.setProperty('--bs-primary-bg-subtle', `rgba(${rgb}, 0.16)`);
        root.style.setProperty('--bs-primary-border-subtle', `rgba(${rgb}, 0.35)`);
        root.style.setProperty('--bs-primary-text-emphasis', color);
        root.style.setProperty('--bs-btn-bg',            color);
        root.style.setProperty('--bs-btn-border-color',  color);
        root.style.setProperty('--bs-btn-hover-bg',      hoverColor);
        root.style.setProperty('--bs-btn-hover-border-color', shadeColor(color, -12));
        root.style.setProperty('--bs-btn-active-bg',     activeColor);
        root.style.setProperty('--bs-link-color',        color);
        root.style.setProperty('--bs-link-color-rgb',    rgb);
        root.style.setProperty('--bs-link-hover-color',  shadeColor(color, -20));

        // Sneat / template & custom vars
        root.style.setProperty('--bs-primary-color',     color);
        root.style.setProperty('--bs-menu-active-bg',    color);
        root.style.setProperty('--bs-menu-active-color', '#ffffff');
        root.style.setProperty('--bs-menu-sub-active-bg', color);
        root.style.setProperty('--bs-menu-sub-active-color', '#ffffff');
        root.style.setProperty('--bs-focus-ring-color',  `rgba(${rgb}, 0.25)`);
        root.style.setProperty('--astro-primary-hover',  hoverColor);
        root.style.setProperty('--astro-primary-active', activeColor);

        // Persist
        localStorage.setItem('astro_primary_color', color);
    }

    // Restore saved primary color on page load
    const savedPrimary = localStorage.getItem('astro_primary_color');
    if (savedPrimary) {
        applyPrimaryColor(savedPrimary);
    }

    // Wire up swatches
    const swatches = document.querySelectorAll('.customizer-primary-swatch:not(.customizer-primary-custom)');
    const customInput = document.getElementById('customizerCustomColor');

    function setActiveSwatch(color) {
        // Remove active class from all
        document.querySelectorAll('.customizer-primary-swatch').forEach(s => s.classList.remove('active'));
        // Find and activate the matching swatch (if preset)
        swatches.forEach(s => {
            if (s.dataset.color && s.dataset.color.toLowerCase() === color.toLowerCase()) {
                s.classList.add('active');
            }
        });
    }

    // Restore active swatch state
    if (savedPrimary) {
        setActiveSwatch(savedPrimary);
        if (customInput) customInput.value = savedPrimary;
    }

    swatches.forEach(function(swatch) {
        swatch.addEventListener('click', function() {
            const color = this.dataset.color;
            applyPrimaryColor(color);
            setActiveSwatch(color);
            if (customInput) customInput.value = color;
        });
    });

    // Custom color picker
    if (customInput) {
        customInput.addEventListener('input', function() {
            applyPrimaryColor(this.value);
            setActiveSwatch(this.value);
        });
        // Click on the rainbow swatch opens the color picker
        const customSwatch = document.querySelector('.customizer-primary-custom');
        if (customSwatch) {
            customSwatch.addEventListener('click', function(e) {
                if (e.target !== customInput) customInput.click();
            });
        }
    }

    // Reset: also clear primary color
    const resetBtn = document.getElementById('themeCustomizerReset');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            localStorage.removeItem('astro_primary_color');
            const propsToRemove = [
                '--bs-primary', '--bs-primary-rgb', '--bs-primary-bg-subtle',
                '--bs-primary-border-subtle', '--bs-primary-text-emphasis',
                '--bs-btn-bg', '--bs-btn-border-color', '--bs-btn-hover-bg',
                '--bs-btn-hover-border-color', '--bs-btn-active-bg',
                '--bs-link-color', '--bs-link-color-rgb', '--bs-link-hover-color',
                '--bs-primary-color', '--bs-menu-active-bg', '--bs-menu-active-color',
                '--bs-menu-sub-active-bg', '--bs-menu-sub-active-color',
                '--bs-focus-ring-color', '--astro-primary-hover', '--astro-primary-active'
            ];
            propsToRemove.forEach(p => document.documentElement.style.removeProperty(p));
            setActiveSwatch('#0b284e');
        });
    }
});
</script>
