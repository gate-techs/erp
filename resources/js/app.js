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

window.flatpickr = flatpickr;

// Flatpickr Initialization
document.addEventListener('DOMContentLoaded', function () {
  const initFlatpickr = () => {
    // Determine language safely
    const htmlLang = document.documentElement.lang || '';
    const isRtl = document.documentElement.dir === 'rtl' || htmlLang.toLowerCase().startsWith('ar');
    const currentLocale = isRtl ? Arabic : 'en';
    const currentPosition = isRtl ? 'auto right' : 'auto left';

    // Normal Date Pickers
    const dateInputs = document.querySelectorAll('.js-flatpickr-date');
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
    const datetimeInputs = document.querySelectorAll('.js-flatpickr-datetime');
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
            altInput: true,
            altFormat: 'd/m/Y H:i',
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
            } else if (node.querySelectorAll && node.querySelectorAll('.js-flatpickr-date, .js-flatpickr-datetime').length > 0) {
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
            domState.contentWidth = 'full'; // Default to full if not found
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
            if (['sticky', 'static', 'hidden'].includes(state.navbarType)) {
                overrides.navbarType = state.navbarType === 'hidden' ? 'hidden' : 'sticky';
            } else {
                overrides.navbarType = 'sticky';
            }
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

        // Force the class on HTML since this version of Vuexy helpers.js misses the large-screen block
        if (isCollapsed) {
            document.documentElement.classList.add('layout-menu-collapsed');
        } else {
            document.documentElement.classList.remove('layout-menu-collapsed');
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

// --- Phone Inputs (intl-tel-input) v29.5.1 ---
import intlTelInput from 'intl-tel-input/intlTelInputWithUtils';
import { ar } from 'intl-tel-input/locale';

document.addEventListener('DOMContentLoaded', function() {
    const phoneInputs = document.querySelectorAll('.js-phone-input');

    // Determine language family once for all inputs
    const lang = document.documentElement.lang || 'en';
    const isArabic = lang.toLowerCase().startsWith('ar');

    phoneInputs.forEach(function(phoneInput) {
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
    });
});

// --- Sidebar Menu Tooltips & Flyouts (Collapsed Mode) ---
document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;

    const isRtl = document.documentElement.getAttribute('dir') === 'rtl';
    const menuTooltips = document.querySelectorAll('[data-menu-tooltip]');

    // 1. Tooltips
    menuTooltips.forEach(function(el) {
        const title = el.getAttribute('data-menu-tooltip');
        if (!title) return;

        const tooltip = new bootstrap.Tooltip(el, {
            title: title,
            placement: isRtl ? 'left' : 'right',
            trigger: 'hover',
            container: 'body'
        });

        el.addEventListener('show.bs.tooltip', function(e) {
            const isCollapsed = document.documentElement.classList.contains('layout-menu-collapsed');
            // Prevent tooltip if not collapsed
            if (!isCollapsed) {
                e.preventDefault();
                return;
            }

            // Prevent tooltip if this is a parent item and its flyout is OPEN
            if (el.classList.contains('menu-toggle')) {
                const parentItem = el.closest('.menu-item');
                if (parentItem && parentItem.classList.contains('open')) {
                    e.preventDefault();
                }
            }
        });
    });

    // 2. Click Outside & Escape to close flyouts
    document.addEventListener('click', function(e) {
        const isCollapsed = document.documentElement.classList.contains('layout-menu-collapsed');
        if (!isCollapsed) return;

        const sidebar = document.getElementById('layout-menu');
        // If click is NOT inside the sidebar, close all open flyouts instantly
        if (sidebar && !sidebar.contains(e.target)) {
            const openItems = sidebar.querySelectorAll('.menu-inner > .menu-item.open');
            openItems.forEach(item => item.classList.remove('open'));
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const isCollapsed = document.documentElement.classList.contains('layout-menu-collapsed');
            if (!isCollapsed) return;

            const openItems = document.querySelectorAll('#layout-menu .menu-inner > .menu-item.open');
            openItems.forEach(item => item.classList.remove('open'));
        }
    });
});

/**
 * ==========================================================================
 * Global FullCalendar Sneat Factory
 * Standardizes toolbar layout, title format (e.g. October 2026), and clean chevrons
 * ==========================================================================
 */
window.createAppCalendar = function (calendarEl, customOptions = {}) {
    if (typeof FullCalendar === 'undefined' || !calendarEl) {
        console.warn('FullCalendar or target calendar element not found.');
        return null;
    }

    const htmlLang = document.documentElement.lang || 'ar';
    const isRtl = document.documentElement.dir === 'rtl' || htmlLang.toLowerCase().startsWith('ar');

    const defaultOptions = {
        locale: isRtl ? 'ar' : 'en',
        direction: isRtl ? 'rtl' : 'ltr',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev next title',
            center: '',
            right: 'addEventButton dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        datesSet: function (info) {
            const titleEl = calendarEl.querySelector('.fc-toolbar-title');
            if (titleEl) {
                const d = info.view.calendar.getDate();
                const monthName = d.toLocaleString('en-US', { month: 'long' });
                const year = d.getFullYear();
                if (info.view.type === 'dayGridMonth' || info.view.type === 'listMonth') {
                    titleEl.textContent = `${monthName} ${year}`;
                } else if (info.view.type === 'timeGridDay') {
                    titleEl.textContent = `${monthName} ${d.getDate()}, ${year}`;
                } else {
                    const start = info.start;
                    const end = new Date(info.end.getTime() - 86400000);
                    const sMonth = start.toLocaleString('en-US', { month: 'short' });
                    const eMonth = end.toLocaleString('en-US', { month: 'short' });
                    titleEl.textContent = `${sMonth} ${start.getDate()} – ${eMonth} ${end.getDate()}, ${year}`;
                }
            }

            if (typeof customOptions.onDatesSet === 'function') {
                customOptions.onDatesSet(info);
            }
        },
        buttonText: {
            today: isRtl ? 'اليوم' : 'Today',
            month: isRtl ? 'شهري' : 'Month',
            week: isRtl ? 'أسبوعي' : 'Week',
            day: isRtl ? 'يومي' : 'Day',
            list: isRtl ? 'أجندة' : 'List'
        },
        height: 'auto',
        navLinks: true,
        dayMaxEvents: 4,
        editable: false,
        selectable: true,
        selectMirror: true
    };

    const mergedOptions = {
        ...defaultOptions,
        ...customOptions,
        headerToolbar: customOptions.headerToolbar || defaultOptions.headerToolbar,
        customButtons: {
            ...(defaultOptions.customButtons || {}),
            ...(customOptions.customButtons || {})
        },
        buttonText: {
            ...(defaultOptions.buttonText || {}),
            ...(customOptions.buttonText || {})
        }
    };

    const calendar = new FullCalendar.Calendar(calendarEl, mergedOptions);
    calendar.render();
    return calendar;
};
