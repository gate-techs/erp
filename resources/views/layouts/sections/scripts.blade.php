<!-- BEGIN: Vendor JS-->

@vite(['resources/assets/vendor/libs/jquery/jquery.js', 'resources/assets/vendor/libs/popper/popper.js', 'resources/assets/vendor/js/bootstrap.js'])

@vite(['resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js', 'resources/assets/vendor/js/menu.js'])

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
});
</script>
