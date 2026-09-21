import './bootstrap';
/*
  Add custom scripts here
*/
import.meta.glob([
  '../assets/img/**',
  // '../assets/json/**',
  '../assets/vendor/fonts/**'
]);

import 'flatpickr/dist/flatpickr.css';
import flatpickr from 'flatpickr';
import { Arabic } from 'flatpickr/dist/l10n/ar.js';

// Flatpickr Initialization
document.addEventListener('DOMContentLoaded', function () {
  const initFlatpickr = () => {
    // Determine language safely
    const htmlLang = document.documentElement.lang || '';
    const isRtl = document.documentElement.dir === 'rtl' || htmlLang.toLowerCase().startsWith('ar');
    const currentLocale = isRtl ? Arabic : 'en';
    const currentPosition = isRtl ? 'auto right' : 'auto left';

    // Normal Date Pickers
    const dateInputs = document.querySelectorAll('.js-flatpickr-date:not(.flatpickr-input)');
    if (dateInputs.length > 0) {
      dateInputs.forEach(input => {
        if (!input._flatpickr) {
          input.type = 'text';
          flatpickr(input, {
            locale: currentLocale,
            position: currentPosition,
            dateFormat: 'Y-m-d',
            allowInput: true,
            altInput: false,
            disableMobile: true
          });
        }
      });
    }

    // Datetime Pickers
    const datetimeInputs = document.querySelectorAll('.js-flatpickr-datetime:not(.flatpickr-input)');
    if (datetimeInputs.length > 0) {
      datetimeInputs.forEach(input => {
        if (!input._flatpickr) {
          input.type = 'text';
          flatpickr(input, {
            locale: currentLocale,
            position: currentPosition,
            enableTime: true,
            dateFormat: 'Y-m-d\\TH:i',
            time_24hr: true,
            allowInput: true,
            altInput: false,
            disableMobile: true
          });
        }
      });
    }
  };

  initFlatpickr();

  // Handle dynamically added fields (e.g. repeaters)
  const observer = new MutationObserver((mutations) => {
    let shouldInit = false;
    mutations.forEach((mutation) => {
      if (mutation.addedNodes && mutation.addedNodes.length > 0) {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType === 1) {
            if (node.classList && (node.classList.contains('js-flatpickr-date') || node.classList.contains('js-flatpickr-datetime'))) {
              shouldInit = true;
            } else if (node.querySelectorAll && node.querySelectorAll('.js-flatpickr-date:not(.flatpickr-input), .js-flatpickr-datetime:not(.flatpickr-input)').length > 0) {
              shouldInit = true;
            }
          }
        });
      }
    });
    if (shouldInit) {
      initFlatpickr();
    }
  });

  observer.observe(document.body, { childList: true, subtree: true });
});


/* Theme Customizer */
document.addEventListener('DOMContentLoaded', function() {
    const customizer = document.getElementById('themeCustomizer');
    if (!customizer) return;

    const toggleBtn = document.getElementById('themeCustomizerToggle');
    const closeBtn = document.getElementById('themeCustomizerClose');
    const backdrop = document.getElementById('themeCustomizerBackdrop');
    const resetBtn = document.getElementById('themeCustomizerReset');

    const skinRadios = document.querySelectorAll('input[name="customizer_skin"]');
    const widthRadios = document.querySelectorAll('input[name="customizer_content_width"]');
    const rtlCheckbox = document.getElementById('customizer_rtl');
    const menuCollapsedCheckbox = document.getElementById('customizer_menu_collapsed');
    const menuHiddenCheckbox = document.getElementById('customizer_menu_hidden');
    const navbarColorRadios = document.querySelectorAll('input[name="customizer_navbar_color"]');
    const navbarTypeRadios = document.querySelectorAll('input[name="customizer_navbar_type"]');
    const footerTypeRadios = document.querySelectorAll('input[name="customizer_footer_type"]');

    function toggleCustomizer() {
        customizer.classList.toggle('open');
        backdrop.classList.toggle('show');
    }
    toggleBtn?.addEventListener('click', toggleCustomizer);
    closeBtn?.addEventListener('click', toggleCustomizer);
    backdrop?.addEventListener('click', toggleCustomizer);

    const validNavbarColors = [
        'bg-navbar-theme', 'bg-primary', 'bg-secondary',
        'bg-success', 'bg-danger', 'bg-warning',
        'bg-info', 'bg-dark'
    ];

    function getCurrentLocale() {
        return (document.documentElement.lang || 'en').split('-')[0].toLowerCase();
    }

    // 1. Read DOM Baseline
    function readCurrentLayoutState() {
        const domState = {};

        // Skin
        domState.skin = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';

        // Content Width
        const contentContainer = document.querySelector('.content-wrapper > div.flex-grow-1');
        if (contentContainer) {
            domState.contentWidth = contentContainer.classList.contains('container-fluid') ? 'full' : 'boxed';
        } else {
            domState.contentWidth = 'boxed';
        }

        // Direction
        const locale = getCurrentLocale();
        domState.directionByLocale = {};
        domState.directionByLocale[locale] = document.documentElement.getAttribute('dir') === 'rtl' ? 'rtl' : 'ltr';

        // Menu Collapsed
        domState.menuCollapsed = document.documentElement.classList.contains('layout-menu-collapsed');

        // Menu Hidden
        const layoutWrapper = document.querySelector('.layout-content-navbar');
        domState.menuHidden = layoutWrapper ? layoutWrapper.classList.contains('layout-without-menu') : false;

        // Navbar Color
        const navbar = document.getElementById('layout-navbar');
        domState.navbarColor = 'bg-navbar-theme';
        if (navbar) {
            for (const cls of validNavbarColors) {
                if (navbar.classList.contains(cls)) {
                    domState.navbarColor = cls;
                    break;
                }
            }
        }

        // Navbar Type
        if (document.documentElement.classList.contains('layout-navbar-fixed')) {
            domState.navbarType = 'sticky';
        } else if (navbar && navbar.style.display === 'none') {
            domState.navbarType = 'hidden';
        } else {
            domState.navbarType = 'static';
        }

        // Footer Type
        const footer = document.querySelector('.content-footer');
        if (document.documentElement.classList.contains('layout-footer-fixed')) {
            domState.footerType = 'sticky';
        } else if (footer && footer.style.display === 'none') {
            domState.footerType = 'hidden';
        } else {
            domState.footerType = 'static';
        }

        return domState;
    }

    // 2. Validate Partial Saved Overrides
    function readValidatedSavedOverrides() {
        try {
            const saved = localStorage.getItem('vuexy_customizer_state');
            if (!saved) return {};

            const state = JSON.parse(saved);
            if (typeof state !== 'object') return {};

            const overrides = {};
            if (['light', 'dark'].includes(state.skin)) overrides.skin = state.skin;
            if (['boxed', 'full'].includes(state.contentWidth)) overrides.contentWidth = state.contentWidth;
            if (state.directionByLocale && typeof state.directionByLocale === 'object') {
                overrides.directionByLocale = state.directionByLocale; // We will validate individual locales on use
            }
            if (typeof state.menuCollapsed === 'boolean') overrides.menuCollapsed = state.menuCollapsed;
            if (typeof state.menuHidden === 'boolean') overrides.menuHidden = state.menuHidden;
            if (validNavbarColors.includes(state.navbarColor)) overrides.navbarColor = state.navbarColor;
            if (['sticky', 'static', 'hidden'].includes(state.navbarType)) overrides.navbarType = state.navbarType;
            if (['sticky', 'static', 'hidden'].includes(state.footerType)) overrides.footerType = state.footerType;

            return overrides;
        } catch (e) {
            return {};
        }
    }

    function saveOverrides() {
        localStorage.setItem('vuexy_customizer_state', JSON.stringify(overrides));
    }

    const domState = readCurrentLayoutState();
    let overrides = readValidatedSavedOverrides();
    let effectiveState = { ...domState, ...overrides };

    // Merge direction specifically for current locale
    const currentLocale = getCurrentLocale();
    if (overrides.directionByLocale && overrides.directionByLocale[currentLocale] && ['rtl', 'ltr'].includes(overrides.directionByLocale[currentLocale])) {
        effectiveState.directionByLocale[currentLocale] = overrides.directionByLocale[currentLocale];
    } else {
        // Ensure effectiveState has the DOM baseline for current locale if no valid override
        if (!effectiveState.directionByLocale) effectiveState.directionByLocale = {};
        effectiveState.directionByLocale[currentLocale] = domState.directionByLocale[currentLocale];
    }

    // 3. Apply ONLY explicit overrides that need post-DOM manipulation
    // Skin, Dir, NavFixed, FooterFixed are already applied by <head> script.

    function applyContentWidth(width) {
        const layoutNavbar = document.getElementById('layout-navbar');
        const navbarContainer = layoutNavbar ? (layoutNavbar.classList.contains('navbar-detached') ? layoutNavbar : layoutNavbar.firstElementChild) : null;
        const contentContainer = document.querySelector('.content-wrapper > div.flex-grow-1');
        const footerContainer = document.querySelector('footer.content-footer > div');

        const containers = [navbarContainer, contentContainer, footerContainer].filter(Boolean);

        containers.forEach(container => {
            if (width === 'full') {
                container.classList.remove('container-xxl');
                container.classList.add('container-fluid');
            } else {
                container.classList.remove('container-fluid');
                container.classList.add('container-xxl');
            }
        });
    }

    function applyMenuHidden(isHidden) {
        const layoutWrapper = document.querySelector('.layout-content-navbar');
        const menu = document.getElementById('layout-menu');
        if (isHidden) {
            layoutWrapper?.classList.add('layout-without-menu');
            if (menu) menu.style.display = 'none';
        } else {
            layoutWrapper?.classList.remove('layout-without-menu');
            if (menu) {
                menu.style.display = '';
                // Clean up any empty inline style attribute if it was the only property
                if (menu.getAttribute('style') === '') menu.removeAttribute('style');
            }
        }
    }

    function applyMenuCollapsed(isCollapsed) {
        if (window.Helpers && window.Helpers.setCollapsed) {
            window.Helpers.setCollapsed(isCollapsed, false);
        }
    }

    function applyNavbarColor(colorClass) {
        const navbar = document.getElementById('layout-navbar');
        if (navbar) {
            validNavbarColors.forEach(cls => navbar.classList.remove(cls));
            navbar.classList.add(colorClass);
            if (colorClass !== 'bg-navbar-theme') {
                navbar.classList.add('navbar-dark');
            } else {
                navbar.classList.remove('navbar-dark');
            }
        }
    }

    function applyNavbarTypeDisplay(type) {
        const navbar = document.getElementById('layout-navbar');
        if (navbar) {
            if (type === 'hidden') {
                navbar.style.display = 'none';
            } else {
                navbar.style.display = '';
                if (navbar.getAttribute('style') === '') navbar.removeAttribute('style');
            }
        }
    }

    function applyFooterTypeDisplay(type) {
        const footer = document.querySelector('.content-footer');
        if (footer) {
            if (type === 'hidden') {
                footer.style.display = 'none';
            } else {
                footer.style.display = '';
                if (footer.getAttribute('style') === '') footer.removeAttribute('style');
            }
        }
    }

    // Only apply if it was explicitly saved
    if ('contentWidth' in overrides) applyContentWidth(overrides.contentWidth);
    if ('menuHidden' in overrides) applyMenuHidden(overrides.menuHidden);
    if ('navbarColor' in overrides) applyNavbarColor(overrides.navbarColor);
    if ('navbarType' in overrides) applyNavbarTypeDisplay(overrides.navbarType);
    if ('footerType' in overrides) applyFooterTypeDisplay(overrides.footerType);

    setTimeout(() => {
        if ('menuCollapsed' in overrides) {
            applyMenuCollapsed(overrides.menuCollapsed);
        }
    }, 100);

    // 4. Bind UI to update overrides and DOM

    skinRadios.forEach(radio => {
        if (radio.value === effectiveState.skin) radio.checked = true;
        radio.addEventListener('change', (e) => {
            overrides.skin = e.target.value;
            document.documentElement.setAttribute('data-bs-theme', overrides.skin);
            localStorage.setItem('astro_theme', overrides.skin);
            saveOverrides();
        });
    });

    widthRadios.forEach(radio => {
        if (radio.value === effectiveState.contentWidth) radio.checked = true;
        radio.addEventListener('change', (e) => {
            overrides.contentWidth = e.target.value;
            applyContentWidth(overrides.contentWidth);
            saveOverrides();
        });
    });

    if (effectiveState.directionByLocale[currentLocale]) {
        rtlCheckbox.checked = effectiveState.directionByLocale[currentLocale] === 'rtl';
    }
    rtlCheckbox.addEventListener('change', (e) => {
        const isRtl = e.target.checked;
        if (!overrides.directionByLocale) overrides.directionByLocale = {};
        overrides.directionByLocale[currentLocale] = isRtl ? 'rtl' : 'ltr';
        document.documentElement.setAttribute('dir', overrides.directionByLocale[currentLocale]);
        saveOverrides();
    });

    menuCollapsedCheckbox.checked = effectiveState.menuCollapsed;
    menuCollapsedCheckbox.addEventListener('change', (e) => {
        overrides.menuCollapsed = e.target.checked;
        applyMenuCollapsed(overrides.menuCollapsed);
        saveOverrides();
    });

    menuHiddenCheckbox.checked = effectiveState.menuHidden;
    menuHiddenCheckbox.addEventListener('change', (e) => {
        overrides.menuHidden = e.target.checked;
        applyMenuHidden(overrides.menuHidden);
        saveOverrides();
    });

    navbarColorRadios.forEach(radio => {
        if (radio.value === effectiveState.navbarColor) radio.checked = true;
        radio.addEventListener('change', (e) => {
            overrides.navbarColor = e.target.value;
            applyNavbarColor(overrides.navbarColor);
            saveOverrides();
        });
    });

    navbarTypeRadios.forEach(radio => {
        if (radio.value === effectiveState.navbarType) radio.checked = true;
        radio.addEventListener('change', (e) => {
            overrides.navbarType = e.target.value;
            const html = document.documentElement;
            if (overrides.navbarType === 'sticky') {
                html.classList.add('layout-navbar-fixed');
            } else {
                html.classList.remove('layout-navbar-fixed');
            }
            applyNavbarTypeDisplay(overrides.navbarType);
            saveOverrides();
        });
    });

    footerTypeRadios.forEach(radio => {
        if (radio.value === effectiveState.footerType) radio.checked = true;
        radio.addEventListener('change', (e) => {
            overrides.footerType = e.target.value;
            const html = document.documentElement;
            if (overrides.footerType === 'sticky') {
                html.classList.add('layout-footer-fixed');
            } else {
                html.classList.remove('layout-footer-fixed');
            }
            applyFooterTypeDisplay(overrides.footerType);
            saveOverrides();
        });
    });

    // 5. Reset to Defaults
    resetBtn?.addEventListener('click', function() {
        const msg = this.getAttribute('data-confirm-msg') || 'Are you sure you want to reset all theme settings to defaults?';
        if (confirm(msg)) {
            localStorage.removeItem('vuexy_customizer_state');
            localStorage.removeItem('astro_theme');
            window.location.reload();
        }
    });

});

// --- Phone Input Pilot (intl-tel-input) v29.5.1 ---
import intlTelInput from 'intl-tel-input/intlTelInputWithUtils';
import { ar } from 'intl-tel-input/locale';

document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.querySelector('.js-phone-input-pilot');
    if (phoneInput) {
        const originalValue = phoneInput.value;
        let isDirty = false;

        phoneInput.addEventListener('input', function() { isDirty = true; });
        phoneInput.addEventListener('countrychange', function() { isDirty = true; });

        if (phoneInput.form) {
            phoneInput.form.addEventListener('submit', function() {
                if (!isDirty) {
                    phoneInput.value = originalValue;
                }
            });
        }

        const hadServerError = phoneInput.classList.contains('is-invalid');
        const val = phoneInput.value.trim();
        let initialCountry = ""; // Blank globe by default

        // Let the package auto-detect + numbers naturally.
        // We only hint "eg" for legacy numbers starting with 01X, so they validate
        // without mutating the input's actual DOM value.
        if (!val.startsWith('+') && (val.startsWith('010') || val.startsWith('011') || val.startsWith('012') || val.startsWith('015'))) {
            initialCountry = "eg";
        }

        // Determine language family
        const lang = document.documentElement.lang || 'en';
        const isArabic = lang.toLowerCase().startsWith('ar');

        const iti = intlTelInput(phoneInput, {
            initialCountry: initialCountry,
            countrySearch: true,
            countrySelectorMode: "DROPDOWN",
            separateDialCode: false,
            ...(isArabic ? {
                uiTranslations: ar,
                countryNameLocale: "ar"
            } : {})
        });

        phoneInput.addEventListener('blur', function() {
            const wrapper = phoneInput.closest('.iti');

            // Clear only client-side errors
            let clientFeedback = wrapper.parentElement.querySelector('.invalid-feedback.iti-error');
            if (clientFeedback) clientFeedback.remove();

            // Never remove a Laravel server error
            if (!hadServerError) {
                phoneInput.classList.remove('is-invalid');
            }

            if (phoneInput.value.trim() && !iti.isValidNumber()) {
                phoneInput.classList.add('is-invalid');

                // Only inject client error if server didn't already complain
                if (!hadServerError) {
                    clientFeedback = document.createElement('div');
                    clientFeedback.className = 'invalid-feedback d-block iti-error';

                    const errorCode = iti.getValidationError();
                    const errorMap = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];
                    clientFeedback.textContent = errorMap[errorCode] || "Invalid phone number";

                    // Insert BELOW the entire .iti wrapper so it doesn't break input styling
                    wrapper.insertAdjacentElement('afterend', clientFeedback);
                }
            }
        });
    }
});
